<?php

namespace App\Services;

use App\Models\Employer;
use App\Models\Ward;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployerExcelService
{
    private const HEADERS = [
        'Tên doanh nghiệp (*)', 'Mã số thuế', 'Lĩnh vực hoạt động', 'Khu vực',
        'Quy mô lao động', 'Phường / Xã', 'Địa chỉ', 'Người liên hệ',
        'Số điện thoại', 'Email', 'Website', 'Mã nguồn', 'Trạng thái',
    ];

    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mau_Nhap_Lieu');
        $lastColumn = 'M';

        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->setCellValue('A1', 'KHUNG NHẬP DANH SÁCH DOANH NGHIỆP');
        $sheet->mergeCells("A2:{$lastColumn}2");
        $sheet->setCellValue('A2', 'Mỗi doanh nghiệp một dòng. Hệ thống ưu tiên đối chiếu theo mã số thuế, sau đó theo mã nguồn và tên. Ô tùy chọn để trống sẽ không xóa dữ liệu cũ.');
        $this->styleTitle($sheet, "A1:{$lastColumn}1");
        $this->styleNote($sheet, "A2:{$lastColumn}2");
        $sheet->fromArray([self::HEADERS], null, 'A4');
        $this->styleHeader($sheet, "A4:{$lastColumn}4");
        $sheet->freezePane('A5');
        $sheet->setAutoFilter("A4:{$lastColumn}5000");

        $widths = [36, 18, 28, 24, 18, 30, 42, 24, 18, 28, 32, 18, 20];
        foreach ($widths as $index => $width) {
            $sheet->getColumnDimension(chr(65 + $index))->setWidth($width);
        }

        $catalog = $spreadsheet->createSheet();
        $catalog->setTitle('Danh_Muc');
        $catalog->fromArray([['Trạng thái', 'Phường / Xã']], null, 'A1');
        $catalog->fromArray([['Hoạt động'], ['Ngừng hoạt động']], null, 'A2');
        $wards = Ward::query()->orderBy('name')->pluck('full_name')->filter()->values();
        foreach ($wards as $index => $ward) {
            $catalog->setCellValue('B'.($index + 2), $ward);
        }
        $this->styleHeader($catalog, 'A1:B1');
        $catalog->getColumnDimension('A')->setWidth(24);
        $catalog->getColumnDimension('B')->setWidth(36);

        $this->listValidation($sheet, 'M5:M5000', 'Danh_Muc!$A$2:$A$3');
        if ($wards->isNotEmpty()) {
            $this->listValidation($sheet, 'F5:F5000', 'Danh_Muc!$B$2:$B$'.($wards->count() + 1));
        }

        return $this->download($spreadsheet, 'khung_nhap_doanh_nghiep.xlsx');
    }

    public function exportCurrentEmployers(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh_Sach');
        $sheet->fromArray([self::HEADERS], null, 'A1');
        $this->styleHeader($sheet, 'A1:M1');
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:M1');

        $row = 2;
        Employer::query()->with('ward')->orderBy('name')->each(function (Employer $employer) use ($sheet, &$row): void {
            $sheet->fromArray([[
                $employer->name,
                $employer->tax_code,
                $employer->industry,
                $employer->region,
                $employer->employee_count,
                $employer->ward?->full_name,
                $employer->address,
                $employer->contact_person,
                $employer->phone,
                $employer->email,
                $employer->website,
                $employer->source_code,
                $employer->status === 'INACTIVE' ? 'Ngừng hoạt động' : 'Hoạt động',
            ]], null, "A{$row}");
            $row++;
        });

        foreach ([36, 18, 28, 24, 18, 30, 42, 24, 18, 28, 32, 18, 20] as $index => $width) {
            $sheet->getColumnDimension(chr(65 + $index))->setWidth($width);
        }

        return $this->download($spreadsheet, 'danh_sach_doanh_nghiep.xlsx');
    }

    public function importFromFile(string $filePath, bool $dryRun = false): array
    {
        try {
            $rows = IOFactory::load($filePath)->getActiveSheet()->toArray(null, true, true, true);
        } catch (\Throwable $error) {
            return $this->failed('Không thể đọc tệp Excel: '.$error->getMessage());
        }

        [$headerRow, $columns] = $this->findHeaders($rows);
        if ($headerRow === null || ! isset($columns['name'])) {
            return $this->failed('Không tìm thấy cột "Tên doanh nghiệp" trong tệp Excel.');
        }

        $wards = Ward::query()->get(['id', 'code', 'name', 'full_name']);
        $wardIndex = [];
        foreach ($wards as $ward) {
            foreach ([$ward->id, $ward->code, $ward->name, $ward->full_name] as $value) {
                if (filled($value)) {
                    $wardIndex[$this->key($value)] = $ward->id;
                }
            }
        }

        $employers = Employer::query()->get();
        $byTax = $employers->filter(fn (Employer $item): bool => filled($item->tax_code))->keyBy(fn (Employer $item): string => $this->key($item->tax_code));
        $bySource = $employers->filter(fn (Employer $item): bool => filled($item->source_code))->groupBy(fn (Employer $item): string => $this->key($item->source_code));
        $byName = $employers->groupBy(fn (Employer $item): string => $this->key($item->name));

        $plans = [];
        $errors = [];
        $skipped = 0;
        $seen = [];
        foreach ($rows as $rowNumber => $row) {
            if ((int) $rowNumber <= $headerRow) {
                continue;
            }
            if (! collect($row)->contains(fn ($value): bool => $this->value($value) !== '')) {
                $skipped++;

                continue;
            }

            $name = $this->cell($row, $columns, 'name');
            $taxCode = $this->cell($row, $columns, 'tax_code');
            $sourceCode = $this->cell($row, $columns, 'source_code');
            if ($name === '') {
                $errors[] = "Dòng {$rowNumber}: Thiếu tên doanh nghiệp.";

                continue;
            }
            if (mb_strlen($name) > 200 || mb_strlen($taxCode) > 30 || mb_strlen($sourceCode) > 50) {
                $errors[] = "Dòng {$rowNumber}: Tên hoặc mã doanh nghiệp vượt quá độ dài cho phép.";

                continue;
            }

            $rowKey = $taxCode !== '' ? 'tax:'.$this->key($taxCode) : ($sourceCode !== '' ? 'source:'.$this->key($sourceCode) : 'name:'.$this->key($name));
            if (isset($seen[$rowKey])) {
                $errors[] = "Dòng {$rowNumber}: Doanh nghiệp trùng với dòng {$seen[$rowKey]} trong tệp.";

                continue;
            }
            $seen[$rowKey] = $rowNumber;

            $matches = collect();
            if ($taxCode !== '' && $byTax->has($this->key($taxCode))) {
                $matches = collect([$byTax->get($this->key($taxCode))]);
            } elseif ($sourceCode !== '' && $bySource->has($this->key($sourceCode))) {
                $matches = $bySource->get($this->key($sourceCode));
            } elseif ($byName->has($this->key($name))) {
                $matches = $byName->get($this->key($name));
            }
            if ($matches->count() > 1) {
                $errors[] = "Dòng {$rowNumber}: Có nhiều doanh nghiệp trùng tên/mã nguồn; cần bổ sung mã số thuế.";

                continue;
            }
            /** @var Employer|null $existing */
            $existing = $matches->first();
            if ($taxCode !== '' && $byTax->has($this->key($taxCode)) && $existing?->id !== $byTax->get($this->key($taxCode))->id) {
                $errors[] = "Dòng {$rowNumber}: Mã số thuế đang thuộc về doanh nghiệp khác.";

                continue;
            }

            $wardText = $this->cell($row, $columns, 'ward');
            $wardId = null;
            if ($wardText !== '') {
                $wardId = $wardIndex[$this->key($wardText)] ?? null;
                if (! $wardId) {
                    $errors[] = "Dòng {$rowNumber}: Không tìm thấy phường/xã '{$wardText}' trong danh mục.";

                    continue;
                }
            }

            $employeeCount = $this->cell($row, $columns, 'employee_count');
            if ($employeeCount !== '' && (! ctype_digit($employeeCount) || (int) $employeeCount < 0)) {
                $errors[] = "Dòng {$rowNumber}: Quy mô lao động phải là số nguyên không âm.";

                continue;
            }
            $email = $this->cell($row, $columns, 'email');
            if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Dòng {$rowNumber}: Email không hợp lệ.";

                continue;
            }
            $website = $this->cell($row, $columns, 'website');
            if ($website !== '' && ! filter_var($website, FILTER_VALIDATE_URL)) {
                $errors[] = "Dòng {$rowNumber}: Website phải là URL đầy đủ, ví dụ https://domain.vn.";

                continue;
            }

            try {
                $status = $this->parseStatus($this->cell($row, $columns, 'status'), $existing?->status ?? 'ACTIVE');
            } catch (\InvalidArgumentException $error) {
                $errors[] = "Dòng {$rowNumber}: {$error->getMessage()}";

                continue;
            }

            $attributes = ['name' => $name, 'status' => $status, 'source' => 'EXCEL'];
            foreach ([
                'tax_code' => $taxCode,
                'industry' => $this->cell($row, $columns, 'industry'),
                'region' => $this->cell($row, $columns, 'region'),
                'address' => $this->cell($row, $columns, 'address'),
                'contact_person' => $this->cell($row, $columns, 'contact_person'),
                'phone' => $this->cell($row, $columns, 'phone'),
                'email' => $email,
                'website' => $website,
                'source_code' => $sourceCode,
            ] as $field => $value) {
                if ($value !== '' || ! $existing) {
                    $attributes[$field] = $value !== '' ? $value : null;
                }
            }
            if ($employeeCount !== '' || ! $existing) {
                $attributes['employee_count'] = $employeeCount !== '' ? (int) $employeeCount : null;
            }
            if ($wardText !== '' || ! $existing) {
                $attributes['ward_id'] = $wardId;
            }
            $plans[] = ['row' => $rowNumber, 'id' => $existing?->id, 'attributes' => $attributes];
        }

        $created = collect($plans)->whereNull('id')->count();
        $updated = count($plans) - $created;
        if ($errors !== []) {
            return $this->result(count($plans) + count($errors), $created, $updated, $skipped, $errors, false, $dryRun);
        }
        if ($dryRun) {
            return $this->result(count($plans), $created, $updated, $skipped, [], false, true);
        }

        DB::transaction(function () use ($plans): void {
            foreach ($plans as $plan) {
                $employer = $plan['id'] ? Employer::query()->findOrFail($plan['id']) : new Employer;
                $employer->fill($plan['attributes']);
                $employer->save();
            }
        });

        return $this->result(count($plans), $created, $updated, $skipped, [], true, false);
    }

    private function findHeaders(array $rows): array
    {
        $aliases = [
            'name' => ['ten doanh nghiep'], 'tax_code' => ['ma so thue'], 'industry' => ['linh vuc hoat dong', 'nganh nghe'],
            'region' => ['khu vuc'], 'employee_count' => ['quy mo lao dong', 'so lao dong'], 'ward' => ['phuong xa'],
            'address' => ['dia chi'], 'contact_person' => ['nguoi lien he'], 'phone' => ['so dien thoai', 'dien thoai'],
            'email' => ['email'], 'website' => ['website', 'trang web'], 'source_code' => ['ma nguon'],
            'status' => ['trang thai'],
        ];
        foreach ($rows as $rowNumber => $row) {
            $columns = [];
            foreach ($row as $column => $value) {
                $header = $this->header($value);
                foreach ($aliases as $field => $fieldAliases) {
                    if (in_array($header, $fieldAliases, true) || collect($fieldAliases)->contains(fn ($alias): bool => str_contains($header, $alias))) {
                        $columns[$field] = $column;
                    }
                }
            }
            if (isset($columns['name'])) {
                return [(int) $rowNumber, $columns];
            }
        }

        return [null, []];
    }

    private function cell(array $row, array $columns, string $field): string
    {
        return isset($columns[$field]) ? $this->value($row[$columns[$field]] ?? null) : '';
    }

    private function value(mixed $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', (string) $value) ?? '');
    }

    private function key(mixed $value): string
    {
        return Str::of($this->value($value))->ascii()->lower()->squish()->value();
    }

    private function header(mixed $value): string
    {
        return Str::of($this->value($value))->ascii()->lower()->replace(['_', '-', '/', '(', ')', '*'], ' ')->squish()->value();
    }

    private function parseStatus(string $value, string $default): string
    {
        $value = $this->key($value);
        if ($value === '') {
            return $default;
        }

        return match ($value) {
            'hoat dong', 'active' => 'ACTIVE',
            'ngung hoat dong', 'inactive' => 'INACTIVE',
            default => throw new \InvalidArgumentException('Trạng thái chỉ được chọn Hoạt động hoặc Ngừng hoạt động.'),
        };
    }

    private function failed(string $message): array
    {
        return ['success' => false, 'message' => $message, 'total' => 0, 'created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => [], 'committed' => false, 'dry_run' => false];
    }

    private function result(int $total, int $created, int $updated, int $skipped, array $errors, bool $committed, bool $dryRun): array
    {
        return ['success' => true, 'message' => null, 'total' => $total, 'created' => $created, 'updated' => $updated, 'skipped' => $skipped, 'errors' => $errors, 'committed' => $committed, 'dry_run' => $dryRun];
    }

    private function listValidation($sheet, string $range, string $formula): void
    {
        $validation = new DataValidation;
        $validation->setType(DataValidation::TYPE_LIST)->setAllowBlank(true)->setShowDropDown(true)->setShowErrorMessage(true)->setErrorTitle('Giá trị không hợp lệ')->setError('Vui lòng chọn một giá trị trong danh mục.')->setFormula1($formula);
        $sheet->setDataValidation($range, $validation);
    }

    private function styleTitle($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray(['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E3A8A']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]]);
        $sheet->getRowDimension(1)->setRowHeight(32);
    }

    private function styleNote($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray(['font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '64748B']], 'alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_CENTER]]);
        $sheet->getRowDimension(2)->setRowHeight(34);
    }

    private function styleHeader($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true], 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]]]);
    }

    private function download(Spreadsheet $spreadsheet, string $filename): StreamedResponse
    {
        return response()->streamDownload(fn () => (new Xlsx($spreadsheet))->save('php://output'), $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
