<?php

namespace App\Services;

use App\Models\Employer;
use App\Models\ReportingPeriod;
use App\Models\TrainingLevel;
use App\Models\TrainingMajor;
use App\Models\WorkforceDemand;
use App\Support\SkillList;
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

class WorkforceDemandExcelService
{
    private const HEADERS = [
        'Mã nhu cầu', 'Tên doanh nghiệp (*)', 'Mã số thuế doanh nghiệp', 'Kỳ báo cáo (*)',
        'Mã ngành / nghề', 'Ngành / nghề (*)', 'Trình độ (*)', 'Mục đích',
        'Số người cần (*)', 'Kỹ năng yêu cầu', 'Khu vực / địa điểm', 'Độ tuổi',
        'Thu nhập dự kiến', 'Ghi chú', 'Trạng thái', 'Dữ liệu minh họa',
    ];

    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mau_Nhap_Lieu');
        $sheet->mergeCells('A1:P1')->setCellValue('A1', 'KHUNG NHẬP NHU CẦU NHÂN LỰC / VIỆC LÀM');
        $sheet->mergeCells('A2:P2')->setCellValue('A2', 'Mỗi dòng là một nhu cầu theo doanh nghiệp, ngành/nghề, trình độ và kỳ báo cáo. Đây là dữ liệu nhu cầu tổng hợp, không phải tin việc làm có mô tả chi tiết.');
        $this->styleTitle($sheet, 'A1:P1');
        $this->styleNote($sheet, 'A2:P2');
        $sheet->fromArray([self::HEADERS], null, 'A4');
        $this->styleHeader($sheet, 'A4:P4');
        $sheet->freezePane('A5')->setAutoFilter('A4:P5000');
        foreach ([18, 38, 22, 22, 20, 36, 22, 20, 18, 40, 32, 18, 22, 42, 20, 20] as $index => $width) {
            $sheet->getColumnDimension(chr(65 + $index))->setWidth($width);
        }

        $catalog = $spreadsheet->createSheet();
        $catalog->setTitle('Danh_Muc');
        $catalog->fromArray([['Doanh nghiệp', 'Mã số thuế', 'Kỳ báo cáo', 'Mã ngành', 'Ngành / nghề', 'Trình độ', 'Mục đích', 'Trạng thái', 'Dữ liệu minh họa']], null, 'A1');
        $this->styleHeader($catalog, 'A1:I1');
        $employers = Employer::query()->where('status', 'ACTIVE')->orderBy('name')->get(['name', 'tax_code'])->values();
        $periods = ReportingPeriod::query()->orderByDesc('starts_at')->get(['code', 'name'])->values();
        $majors = TrainingMajor::query()->where('is_active', true)->orderBy('name')->get(['code', 'name'])->values();
        $levels = TrainingLevel::query()->where('is_active', true)->orderBy('display_order')->get(['name'])->values();
        $max = max($employers->count(), $periods->count(), $majors->count(), $levels->count(), 3);
        for ($index = 0; $index < $max; $index++) {
            $row = $index + 2;
            $catalog->setCellValue("A{$row}", $employers[$index]->name ?? null);
            $catalog->setCellValue("B{$row}", $employers[$index]->tax_code ?? null);
            $catalog->setCellValue("C{$row}", $periods[$index]->code ?? null);
            $catalog->setCellValue("D{$row}", $majors[$index]->code ?? null);
            $catalog->setCellValue("E{$row}", $majors[$index]->name ?? null);
            $catalog->setCellValue("F{$row}", $levels[$index]->name ?? null);
        }
        foreach (['Tuyển dụng', 'Đào tạo mới', 'Đào tạo lại'] as $index => $value) {
            $catalog->setCellValue('G'.($index + 2), $value);
        }
        foreach (['Bản nháp', 'Đã công bố', 'Đã đóng'] as $index => $value) {
            $catalog->setCellValue('H'.($index + 2), $value);
        }
        $catalog->setCellValue('I2', 'Không')->setCellValue('I3', 'Có');
        foreach (range('A', 'I') as $column) {
            $catalog->getColumnDimension($column)->setWidth(in_array($column, ['A', 'E'], true) ? 38 : 22);
        }
        if ($employers->isNotEmpty()) {
            $this->listValidation($sheet, 'B5:B5000', 'Danh_Muc!$A$2:$A$'.($employers->count() + 1));
        }
        if ($periods->isNotEmpty()) {
            $this->listValidation($sheet, 'D5:D5000', 'Danh_Muc!$C$2:$C$'.($periods->count() + 1));
        }
        if ($majors->isNotEmpty()) {
            $this->listValidation($sheet, 'F5:F5000', 'Danh_Muc!$E$2:$E$'.($majors->count() + 1));
        }
        if ($levels->isNotEmpty()) {
            $this->listValidation($sheet, 'G5:G5000', 'Danh_Muc!$F$2:$F$'.($levels->count() + 1));
        }
        $this->listValidation($sheet, 'H5:H5000', 'Danh_Muc!$G$2:$G$4');
        $this->listValidation($sheet, 'O5:O5000', 'Danh_Muc!$H$2:$H$4');
        $this->listValidation($sheet, 'P5:P5000', 'Danh_Muc!$I$2:$I$3');

        return $this->download($spreadsheet, 'khung_nhap_nhu_cau_nhan_luc.xlsx');
    }

    public function exportCurrentDemands(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh_Sach');
        $sheet->fromArray([self::HEADERS], null, 'A1');
        $this->styleHeader($sheet, 'A1:P1');
        $sheet->freezePane('A2')->setAutoFilter('A1:P1');
        $row = 2;
        WorkforceDemand::query()->with(['employer', 'period', 'major', 'trainingLevel'])->latest('id')->each(function (WorkforceDemand $demand) use ($sheet, &$row): void {
            $sheet->fromArray([[
                $demand->external_id, $demand->employer?->name, $demand->employer?->tax_code,
                $demand->period?->code, $demand->major?->code, $demand->major?->name,
                $demand->trainingLevel?->name, $this->purposeLabel($demand->purpose), $demand->quantity,
                implode(', ', SkillList::parse($demand->skills)), $demand->location, $demand->age_range,
                $demand->salary, $demand->note, $this->statusLabel($demand->status), $demand->is_sample ? 'Có' : 'Không',
            ]], null, "A{$row}");
            $row++;
        });
        foreach ([18, 38, 22, 22, 20, 36, 22, 20, 18, 40, 32, 18, 22, 42, 20, 20] as $index => $width) {
            $sheet->getColumnDimension(chr(65 + $index))->setWidth($width);
        }

        return $this->download($spreadsheet, 'danh_sach_nhu_cau_nhan_luc.xlsx');
    }

    public function importFromFile(string $filePath, bool $dryRun = false): array
    {
        try {
            $rows = IOFactory::load($filePath)->getActiveSheet()->toArray(null, true, true, true);
        } catch (\Throwable $error) {
            return $this->failed('Không thể đọc tệp Excel: '.$error->getMessage());
        }
        [$headerRow, $columns] = $this->findHeaders($rows);
        foreach (['employer', 'period', 'major', 'level', 'quantity'] as $required) {
            if ($headerRow === null || ! isset($columns[$required])) {
                return $this->failed('Thiếu một hoặc nhiều cột bắt buộc: doanh nghiệp, kỳ báo cáo, ngành/nghề, trình độ, số người cần.');
            }
        }

        $employers = Employer::query()->get();
        $employersByTax = $employers->filter(fn ($item) => filled($item->tax_code))->keyBy(fn ($item) => $this->key($item->tax_code));
        $employersByName = $employers->groupBy(fn ($item) => $this->key($item->name));
        $periods = ReportingPeriod::query()->get();
        $periodIndex = $this->indexModels($periods, ['code', 'name']);
        $majors = TrainingMajor::query()->get();
        $majorIndex = $this->indexModels($majors, ['code', 'name', 'slug']);
        $levels = TrainingLevel::query()->get();
        $levelIndex = $this->indexModels($levels, ['id', 'name']);
        $existingByExternalId = WorkforceDemand::query()->whereNotNull('external_id')->get()->keyBy(fn ($item) => $this->key($item->external_id));

        $plans = [];
        $errors = [];
        $skipped = 0;
        $seen = [];
        foreach ($rows as $rowNumber => $row) {
            if ((int) $rowNumber <= $headerRow) {
                continue;
            }
            if (! collect($row)->contains(fn ($value) => $this->value($value) !== '')) {
                $skipped++;

                continue;
            }
            $employerName = $this->cell($row, $columns, 'employer');
            $taxCode = $this->cell($row, $columns, 'employer_tax');
            $employerMatches = $taxCode !== '' && $employersByTax->has($this->key($taxCode))
                ? collect([$employersByTax->get($this->key($taxCode))])
                : ($employersByName->get($this->key($employerName)) ?? collect());
            if ($employerMatches->count() !== 1) {
                $errors[] = "Dòng {$rowNumber}: Không xác định duy nhất doanh nghiệp '{$employerName}'.";

                continue;
            }
            $employer = $employerMatches->first();
            $period = $periodIndex[$this->key($this->cell($row, $columns, 'period'))] ?? null;
            $majorLookup = $this->cell($row, $columns, 'major_code') ?: $this->cell($row, $columns, 'major');
            $major = $majorIndex[$this->key($majorLookup)] ?? null;
            $level = $levelIndex[$this->key($this->cell($row, $columns, 'level'))] ?? null;
            if (! $period) {
                $errors[] = "Dòng {$rowNumber}: Kỳ báo cáo không tồn tại.";

                continue;
            }
            if (! $major) {
                $errors[] = "Dòng {$rowNumber}: Ngành/nghề không tồn tại trong danh mục.";

                continue;
            }
            if (! $level) {
                $errors[] = "Dòng {$rowNumber}: Trình độ không tồn tại trong danh mục.";

                continue;
            }
            $quantity = $this->cell($row, $columns, 'quantity');
            if (! ctype_digit($quantity) || (int) $quantity < 1 || (int) $quantity > 100000) {
                $errors[] = "Dòng {$rowNumber}: Số người cần phải là số nguyên từ 1 đến 100.000.";

                continue;
            }
            try {
                $purpose = $this->parsePurpose($this->cell($row, $columns, 'purpose'), 'RECRUITMENT');
            } catch (\InvalidArgumentException $error) {
                $errors[] = "Dòng {$rowNumber}: {$error->getMessage()}";

                continue;
            }
            $externalId = $this->cell($row, $columns, 'external_id');
            if ($externalId === '') {
                $externalId = 'EXCEL-'.strtoupper(substr(sha1(implode('|', [$employer->id, $period->id, $major->id, $level->id, $purpose, $this->cell($row, $columns, 'location')])), 0, 20));
            }
            if (mb_strlen($externalId) > 100) {
                $errors[] = "Dòng {$rowNumber}: Mã nhu cầu vượt quá 100 ký tự.";

                continue;
            }
            $rowKey = $this->key($externalId);
            if (isset($seen[$rowKey])) {
                $errors[] = "Dòng {$rowNumber}: Mã nhu cầu trùng dòng {$seen[$rowKey]}.";

                continue;
            }
            $seen[$rowKey] = $rowNumber;
            $existing = $existingByExternalId->get($rowKey);
            try {
                $purpose = $this->parsePurpose($this->cell($row, $columns, 'purpose'), $existing?->purpose ?? $purpose);
                $status = $this->parseStatus($this->cell($row, $columns, 'status'), $existing?->status ?? 'DRAFT');
                $isSample = $this->parseBoolean($this->cell($row, $columns, 'is_sample'), (bool) ($existing?->is_sample ?? false));
            } catch (\InvalidArgumentException $error) {
                $errors[] = "Dòng {$rowNumber}: {$error->getMessage()}";

                continue;
            }
            $attributes = [
                'employer_id' => $employer->id, 'reporting_period_id' => $period->id,
                'training_major_id' => $major->id, 'education_level_id' => $level->id,
                'purpose' => $purpose, 'quantity' => (int) $quantity, 'status' => $status,
                'source' => 'EXCEL', 'external_id' => $externalId, 'is_sample' => $isSample,
            ];
            foreach (['location', 'age_range', 'salary', 'note'] as $field) {
                $value = $this->cell($row, $columns, $field);
                if ($value !== '' || ! $existing) {
                    $attributes[$field] = $value !== '' ? $value : null;
                }
            }
            $skillText = $this->cell($row, $columns, 'skills');
            $skillNames = $skillText === '' && $existing
                ? null
                : SkillList::parse($skillText);
            if ($skillNames !== null) {
                $attributes['skills'] = $skillNames;
            }
            $plans[] = ['id' => $existing?->id, 'attributes' => $attributes];
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
                $demand = $plan['id'] ? WorkforceDemand::query()->findOrFail($plan['id']) : new WorkforceDemand;
                $demand->fill($plan['attributes'])->save();
            }
        });

        return $this->result(count($plans), $created, $updated, $skipped, [], true, false);
    }

    private function findHeaders(array $rows): array
    {
        $aliases = [
            'external_id' => ['ma nhu cau'], 'employer' => ['ten doanh nghiep', 'doanh nghiep'], 'employer_tax' => ['ma so thue doanh nghiep', 'ma so thue'],
            'period' => ['ky bao cao', 'ky'], 'major_code' => ['ma nganh nghe', 'ma nganh'], 'major' => ['nganh nghe'], 'level' => ['trinh do'],
            'purpose' => ['muc dich'], 'quantity' => ['so nguoi can', 'so luong'], 'skills' => ['ky nang yeu cau', 'ky nang'],
            'location' => ['khu vuc dia diem', 'dia diem'], 'age_range' => ['do tuoi'], 'salary' => ['thu nhap du kien', 'thu nhap'],
            'note' => ['ghi chu'], 'status' => ['trang thai'], 'is_sample' => ['du lieu minh hoa'],
        ];
        foreach ($rows as $rowNumber => $row) {
            $columns = [];
            foreach ($row as $column => $value) {
                $header = $this->header($value);
                foreach ($aliases as $field => $values) {
                    if (in_array($header, $values, true)) {
                        $columns[$field] = $column;
                    }
                }
            }
            if (isset($columns['employer'], $columns['period'], $columns['major'], $columns['level'], $columns['quantity'])) {
                return [(int) $rowNumber, $columns];
            }
        }

        return [null, []];
    }

    private function indexModels($models, array $fields): array
    {
        $index = [];
        foreach ($models as $model) {
            foreach ($fields as $field) {
                if (filled($model->{$field})) {
                    $index[$this->key($model->{$field})] = $model;
                }
            }
        }

        return $index;
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

    private function parsePurpose(string $value, string $default): string
    {
        return match ($this->key($value)) {
            '' => $default, 'tuyen dung', 'recruitment' => 'RECRUITMENT', 'dao tao moi', 'new training' => 'NEW_TRAINING', 'dao tao lai', 'retraining' => 'RETRAINING', default => throw new \InvalidArgumentException('Mục đích không hợp lệ.')
        };
    }

    private function parseStatus(string $value, string $default): string
    {
        return match ($this->key($value)) {
            '' => $default, 'ban nhap', 'draft' => 'DRAFT', 'da cong bo', 'published' => 'PUBLISHED', 'da dong', 'closed' => 'CLOSED', default => throw new \InvalidArgumentException('Trạng thái không hợp lệ.')
        };
    }

    private function parseBoolean(string $value, bool $default): bool
    {
        return match ($this->key($value)) {
            '' => $default, 'khong', 'no', 'false', '0' => false, 'co', 'yes', 'true', '1' => true, default => throw new \InvalidArgumentException('Dữ liệu minh họa chỉ nhận Có hoặc Không.')
        };
    }

    private function purposeLabel(string $value): string
    {
        return ['RECRUITMENT' => 'Tuyển dụng', 'NEW_TRAINING' => 'Đào tạo mới', 'RETRAINING' => 'Đào tạo lại'][$value] ?? $value;
    }

    private function statusLabel(string $value): string
    {
        return ['DRAFT' => 'Bản nháp', 'PUBLISHED' => 'Đã công bố', 'CLOSED' => 'Đã đóng'][$value] ?? $value;
    }

    private function failed(string $message): array
    {
        return ['success' => false, 'message' => $message, 'total' => 0, 'created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => [], 'committed' => false, 'dry_run' => false];
    }

    private function result(int $total, int $created, int $updated, int $skipped, array $errors, bool $committed, bool $dryRun): array
    {
        return compact('total', 'created', 'updated', 'skipped', 'errors', 'committed') + ['success' => true, 'message' => null, 'dry_run' => $dryRun];
    }

    private function listValidation($sheet, string $range, string $formula): void
    {
        $validation = new DataValidation;
        $validation->setType(DataValidation::TYPE_LIST)->setAllowBlank(true)->setShowDropDown(true)->setShowErrorMessage(true)->setErrorTitle('Giá trị không hợp lệ')->setError('Vui lòng chọn một giá trị trong danh mục.')->setFormula1($formula);
        $sheet->setDataValidation($range, $validation);
    }

    private function styleTitle($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray(['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E3A8A']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
        $sheet->getRowDimension(1)->setRowHeight(32);
    }

    private function styleNote($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray(['font' => ['italic' => true, 'color' => ['rgb' => '64748B']], 'alignment' => ['wrapText' => true]]);
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
