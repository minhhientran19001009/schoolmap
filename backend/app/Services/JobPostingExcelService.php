<?php

namespace App\Services;

use App\Support\SkillList;

use App\Models\CareerField;
use App\Models\Employer;
use App\Models\JobPosting;
use App\Models\Skill;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobPostingExcelService
{
    public const FIELDS = Skill::CAREER_FIELDS;

    private const HEADERS = ['Mã tin (*)', 'Mã số thuế doanh nghiệp', 'Tên doanh nghiệp (*)', 'Tiêu đề công việc (*)', 'Lĩnh vực (*)', 'Số người cần (*)', 'Hạn nộp (dd/mm/yyyy)', 'Địa chỉ làm việc', 'Xã / Phường', 'Mức lương', 'Trình độ', 'Kinh nghiệm', 'Mô tả công việc', 'Yêu cầu', 'Quyền lợi', 'Kỹ năng', 'Trạng thái'];

    public function downloadTemplate(): StreamedResponse
    {
        return $this->workbook(false);
    }

    public function exportCurrentJobs(): StreamedResponse
    {
        return $this->workbook(true);
    }

    private function workbook(bool $export): StreamedResponse
    {
        $book = new Spreadsheet;
        $sheet = $book->getActiveSheet();
        $sheet->setTitle('Tin_Viec_Lam');
        $sheet->fromArray([self::HEADERS]);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:Q1');
        $sheet->getStyle('A1:Q1')->getFont()->setBold(true);
        foreach (range('A', 'Q') as $column) {
            $sheet->getColumnDimension($column)->setWidth(28);
        }
        $sheet->getColumnDimension('C')->setWidth(45);
        $sheet->getColumnDimension('D')->setWidth(45);
        $fields = CareerField::options();
        if ($export) {
            $row = 2;
            foreach (JobPosting::with('employer')->orderBy('id')->cursor() as $job) {
                $d = $job->details;
                $values = [$job->id, $job->employer->tax_code, $job->employer->name, $job->title, $fields[$job->industry] ?? $job->industry, $job->recruitment_count, $job->deadline?->format('d/m/Y'), $d['address'] ?? '', $d['ward_name'] ?? '', $d['salary'] ?? '', $d['education'] ?? '', $d['experience'] ?? '', implode("\n", $d['description'] ?? []), implode("\n", $d['requirements'] ?? []), implode("\n", $d['benefits'] ?? []), implode('; ', $d['skills'] ?? []), $job->status];
                foreach ($values as $index => $value) {
                    $sheet->setCellValueExplicit(chr(65 + $index).$row, (string) $value, DataType::TYPE_STRING);
                }
                $row++;
            }
        }
        $catalog = $book->createSheet();
        $catalog->setTitle('Danh_Muc');
        $catalog->fromArray([['Doanh nghiệp', 'Mã số thuế', 'Lĩnh vực', 'Trạng thái']]);
        $employers = Employer::orderBy('name')->get();
        foreach ($employers as $i => $employer) {
            $catalog->setCellValueExplicit('A'.($i + 2), $employer->name, DataType::TYPE_STRING);
            $catalog->setCellValueExplicit('B'.($i + 2), (string) $employer->tax_code, DataType::TYPE_STRING);
        }
        foreach (array_values($fields) as $i => $field) {
            $catalog->setCellValue('C'.($i + 2), $field);
        }
        foreach (['DRAFT', 'PUBLISHED', 'CLOSED'] as $i => $status) {
            $catalog->setCellValue('D'.($i + 2), $status);
        }
        foreach (['C' => ['A', max(2, $employers->count() + 1)], 'E' => ['C', max(2, count($fields) + 1)], 'Q' => ['D', 4]] as $column => [$source, $last]) {
            $validation = new DataValidation;
            $validation->setType(DataValidation::TYPE_LIST)->setFormula1("Danh_Muc!\${$source}\$2:\${$source}\${$last}")->setShowErrorMessage(true)->setShowDropDown(true);
            $sheet->setDataValidation("{$column}2:{$column}5000", $validation);
        }
        $guide = $book->createSheet();
        $guide->setTitle('Huong_Dan');
        $guide->fromArray(array_map(fn ($line) => [$line], [
            'Mỗi dòng là một tin tuyển dụng. Mã tin bắt buộc, duy nhất; nhập lại cùng mã để cập nhật.',
            'Doanh nghiệp phải có trong hệ thống. Ưu tiên mã số thuế; nếu không có mã, tên phải khớp duy nhất.',
            'Ngày nhập dạng văn bản dd/mm/yyyy hoặc ngày Excel. Mô tả, yêu cầu, quyền lợi: mỗi ý một dòng trong ô.',
            'Kỹ năng ngăn cách bằng dấu chấm phẩy. Ô tùy chọn trống giữ lại dữ liệu cũ.',
            'Trạng thái: DRAFT (nháp), PUBLISHED (công bố), CLOSED (đóng). Tin mới mặc định DRAFT.',
        ]));
        $guide->getColumnDimension('A')->setWidth(120);
        $book->setActiveSheetIndex(0);

        return response()->streamDownload(fn () => (new Xlsx($book))->save('php://output'), $export ? 'danh_sach_tin_viec_lam.xlsx' : 'khung_nhap_tin_viec_lam.xlsx');
    }

    public function importFromFile(string $filePath, bool $dryRun = false): array
    {
        $result = ['success' => true, 'message' => null, 'total' => 0, 'created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => [], 'committed' => false, 'dry_run' => $dryRun];
        try {
            $sheet = IOFactory::load($filePath)->getSheet(0);
            $rows = $sheet->toArray(null, true, false, false);
        } catch (\Throwable $e) {
            return array_replace($result, ['success' => false, 'message' => 'Không đọc được tệp Excel.']);
        }
        $headers = array_shift($rows);
        if ($headers !== self::HEADERS) {
            return array_replace($result, ['success' => false, 'message' => 'Tên và thứ tự cột chưa đúng. Vui lòng dùng khung mẫu tin việc làm.']);
        }
        $fields = CareerField::options();
        $employers = Employer::all();
        $seen = [];
        $plans = [];
        foreach ($rows as $index => $row) {
            if (! collect($row)->contains(fn ($cell) => filled($cell))) {
                $result['skipped']++;

                continue;
            }
            $result['total']++;
            $number = $index + 2;
            $v = array_map(fn ($value) => trim((string) $value), array_pad($row, 17, ''));
            try {
                if ($v[0] === '' || mb_strlen($v[0]) > 100 || isset($seen[$v[0]])) {
                    throw new \RuntimeException('Mã tin thiếu, quá dài hoặc trùng trong tệp.');
                }
                $seen[$v[0]] = true;
                $matches = $employers->filter(fn ($e) => $v[1] !== '' ? (string) $e->tax_code === $v[1] : Str::ascii(mb_strtolower($e->name)) === Str::ascii(mb_strtolower($v[2])));
                if ($matches->count() !== 1) {
                    throw new \RuntimeException('Không tìm thấy duy nhất doanh nghiệp.');
                }
                $existing = JobPosting::find($v[0]);
                $industry = isset($fields[$v[4]]) ? $v[4] : array_search($v[4], $fields, true);
                $attributes = ['id' => $v[0], 'title' => $v[3], 'industry' => $industry ?: null, 'recruitment_count' => $v[5], 'status' => $v[16] ?: ($existing?->status ?? 'DRAFT')];
                Validator::make($attributes, ['title' => 'required|string|max:255', 'industry' => 'required|in:'.implode(',', array_keys($fields)), 'recruitment_count' => 'required|integer|min:1|max:100000', 'status' => 'required|in:DRAFT,PUBLISHED,CLOSED'])->validate();
                $attributes['employer_id'] = $matches->first()->id;
                $attributes['deadline'] = $existing?->deadline;
                if ($v[6] !== '') {
                    if (is_numeric($row[6])) {
                        $date = Date::excelToDateTimeObject((float) $row[6])->format('d/m/Y');
                    } else {
                        $date = $v[6];
                    }
                    $parsed = Carbon::createFromFormat('!d/m/Y', $date);
                    if ($parsed->format('d/m/Y') !== $date) {
                        throw new \RuntimeException('Hạn nộp không phải ngày hợp lệ.');
                    }
                    $attributes['deadline'] = $parsed->toDateString();
                }
                $details = $existing?->details ?? ['benefits' => [], 'description' => [], 'requirements' => [], 'skills' => [], 'training_schools' => [], 'contact' => [], 'salary_level' => 'all', 'is_hot' => false];
                foreach ([7 => 'address', 8 => 'ward_name', 9 => 'salary', 10 => 'education', 11 => 'experience'] as $column => $field) {
                    if ($v[$column] !== '' || ! $existing) {
                        $details[$field] = $v[$column];
                    }
                }
                foreach ([12 => 'description', 13 => 'requirements', 14 => 'benefits', 15 => 'skills'] as $column => $field) {
                    if ($v[$column] !== '' || ! $existing) {
                        $details[$field] = $column === 15
                            ? SkillList::parse($v[$column])
                            : array_values(array_filter(array_map('trim', preg_split('/[\r\n]+/u', $v[$column]))));
                    }
                }
                $details['industry_name'] = $fields[$industry];
                $details['profession'] = $v[3];
                $attributes['details'] = $details;
                $plans[] = $attributes;
                $result[$existing ? 'updated' : 'created']++;
            } catch (\Throwable $e) {
                $result['errors'][] = "Dòng {$number}: ".$e->getMessage();
            }
        }
        if (! $dryRun && ! $result['errors']) {
            DB::transaction(function () use ($plans): void {
                foreach ($plans as $plan) {
                    JobPosting::updateOrCreate(['id' => $plan['id']], $plan);
                }
            });
            $result['committed'] = true;
        }

        return $result;
    }
}
