<?php

namespace App\Services;

use App\Models\TrainingMajor;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TrainingMajorExcelService
{
    public const NAME_HEADER = 'Tên chuyên ngành / nghề đào tạo (*)';
    public const ACTIVE_HEADER = 'Đang sử dụng';

    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mau_Nhap_Lieu');

        $sheet->mergeCells('A1:B1');
        $sheet->setCellValue('A1', 'KHUNG NHẬP DANH MỤC CHUYÊN NGÀNH / NGHỀ ĐÀO TẠO');
        $sheet->mergeCells('A2:B2');
        $sheet->setCellValue('A2', 'Nhập mỗi chuyên ngành một dòng. Không thay đổi tên cột. Tên ngành là bắt buộc; trạng thái để trống sẽ mặc định là Có.');
        $this->styleTitle($sheet, 'A1:B1');
        $this->styleNote($sheet, 'A2:B2');

        $sheet->setCellValue('A4', self::NAME_HEADER);
        $sheet->setCellValue('B4', self::ACTIVE_HEADER);
        $this->styleHeader($sheet, 'A4:B4');
        $sheet->getColumnDimension('A')->setWidth(58);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getRowDimension(4)->setRowHeight(32);
        $sheet->freezePane('A5');
        $sheet->setAutoFilter('A4:B500');

        $validation = new DataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowDropDown(true);
        $validation->setShowErrorMessage(true);
        $validation->setErrorTitle('Trạng thái không hợp lệ');
        $validation->setError('Vui lòng chọn Có hoặc Không.');
        $validation->setFormula1('Danh_Muc!$A$2:$A$3');
        $sheet->setDataValidation('B5:B500', $validation);

        $catalog = $spreadsheet->createSheet();
        $catalog->setTitle('Danh_Muc');
        $catalog->setCellValue('A1', 'Giá trị trạng thái');
        $catalog->setCellValue('A2', 'Có');
        $catalog->setCellValue('A3', 'Không');
        $catalog->getColumnDimension('A')->setWidth(24);
        $this->styleHeader($catalog, 'A1:A1');

        return $this->download($spreadsheet, 'khung_danh_muc_chuyen_nganh.xlsx');
    }

    public function exportCurrentMajors(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh_Sach');
        $sheet->fromArray([
            ['Tên chuyên ngành / nghề đào tạo', 'Đang sử dụng', 'Số trường đào tạo'],
        ], null, 'A1');
        $this->styleHeader($sheet, 'A1:C1');
        $sheet->getColumnDimension('A')->setWidth(58);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:C1');

        $row = 2;
        TrainingMajor::query()
            ->withCount('schools')
            ->orderBy('name')
            ->each(function (TrainingMajor $major) use ($sheet, &$row): void {
                $sheet->setCellValue("A{$row}", $major->name);
                $sheet->setCellValue("B{$row}", $major->is_active ? 'Có' : 'Không');
                $sheet->setCellValue("C{$row}", (int) $major->schools_count);
                $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $row++;
            });

        return $this->download($spreadsheet, 'danh_muc_chuyen_nganh.xlsx');
    }

    public function importFromFile(string $filePath): array
    {
        try {
            $sheet = IOFactory::load($filePath)->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Không thể đọc tệp Excel: ' . $e->getMessage(),
                'total' => 0,
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => [],
            ];
        }

        $headerRow = null;
        $nameColumn = null;
        $activeColumn = null;

        foreach ($rows as $rowNumber => $row) {
            foreach ($row as $column => $value) {
                $header = $this->normalizeHeader($value);
                if ($this->isNameHeader($header)) {
                    $headerRow = (int) $rowNumber;
                    $nameColumn = $column;
                    break 2;
                }
            }
        }

        if ($headerRow === null || $nameColumn === null) {
            return [
                'success' => false,
                'message' => 'Không tìm thấy cột "Tên chuyên ngành / nghề đào tạo" trong tệp Excel.',
                'total' => 0,
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
                'errors' => [],
            ];
        }

        foreach ($rows[$headerRow] ?? [] as $column => $value) {
            $header = $this->normalizeHeader($value);
            if ($this->isActiveHeader($header)) {
                $activeColumn = $column;
                break;
            }
        }

        $total = 0;
        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];
        $seenSlugs = [];

        foreach ($rows as $rowNumber => $row) {
            if ((int) $rowNumber <= $headerRow) {
                continue;
            }

            $name = $this->normalizeValue($row[$nameColumn] ?? null);
            $hasData = collect($row)->contains(fn ($value): bool => $this->normalizeValue($value) !== '');
            if (! $hasData) {
                $skipped++;
                continue;
            }

            $total++;
            if ($name === '') {
                $errors[] = "Dòng {$rowNumber}: Thiếu tên chuyên ngành / nghề đào tạo.";
                continue;
            }
            if (mb_strlen($name, 'UTF-8') > 255) {
                $errors[] = "Dòng {$rowNumber}: Tên chuyên ngành không được vượt quá 255 ký tự.";
                continue;
            }

            $slug = Str::slug($name);
            if ($slug === '') {
                $errors[] = "Dòng {$rowNumber}: Không thể tạo mã định danh từ tên chuyên ngành.";
                continue;
            }
            if (isset($seenSlugs[$slug])) {
                $errors[] = "Dòng {$rowNumber}: Chuyên ngành bị trùng với dòng {$seenSlugs[$slug]}.";
                continue;
            }
            $seenSlugs[$slug] = $rowNumber;

            try {
                $isActive = $this->parseActiveValue($row[$activeColumn] ?? null);
                $major = TrainingMajor::query()->where('slug', $slug)->first();
                $isNew = $major === null;
                $major ??= new TrainingMajor();
                $major->name = $name;
                $major->is_active = $isActive;
                $major->save();

                $isNew ? $created++ : $updated++;
            } catch (\InvalidArgumentException $e) {
                $errors[] = "Dòng {$rowNumber}: {$e->getMessage()}";
            } catch (\Throwable $e) {
                $errors[] = "Dòng {$rowNumber}: Không thể lưu dữ liệu ({$e->getMessage()}).";
            }
        }

        return [
            'success' => true,
            'message' => null,
            'total' => $total,
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    private function normalizeValue(mixed $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', (string) $value));
    }

    private function normalizeHeader(mixed $value): string
    {
        return Str::of($this->normalizeValue($value))
            ->ascii()
            ->lower()
            ->replace(['_', '-', '/', '(', ')', '*'], ' ')
            ->squish()
            ->value();
    }

    private function isNameHeader(string $header): bool
    {
        return str_contains($header, 'chuyen nganh')
            || str_contains($header, 'nghe dao tao')
            || $header === 'name';
    }

    private function isActiveHeader(string $header): bool
    {
        return str_contains($header, 'dang su dung')
            || str_contains($header, 'trang thai')
            || $header === 'is active'
            || $header === 'active';
    }

    private function parseActiveValue(mixed $value): bool
    {
        $active = Str::of($this->normalizeValue($value))->ascii()->lower()->squish()->value();
        if ($active === '') {
            return true;
        }
        if (in_array($active, ['co', 'yes', 'true', '1', 'dang su dung', 'active', 'bat'], true)) {
            return true;
        }
        if (in_array($active, ['khong', 'no', 'false', '0', 'khong su dung', 'inactive', 'tat'], true)) {
            return false;
        }

        throw new \InvalidArgumentException('Trạng thái chỉ được nhập Có hoặc Không.');
    }

    private function styleTitle($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);
    }

    private function styleNote($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '64748B']],
            'alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(30);
    }

    private function styleHeader($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ]);
    }

    private function download(Spreadsheet $spreadsheet, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
