<?php

namespace App\Services;

use App\Models\SchoolSourceRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;
use Throwable;

class TrainingScaleExcelService
{
    public function importFromFile(string $path, bool $dryRun = true): array
    {
        $result = ['success' => false, 'created' => 0, 'updated' => 0, 'unchanged' => 0, 'programs' => 0, 'errors' => [], 'dry_run' => $dryRun];
        try {
            $book = IOFactory::load($path);
            $main = $book->getSheetByName('Mau_Nhap_Lieu');
            $summary = $book->getSheetByName('QM_Tong_hop');
            $details = $book->getSheetByName('QM_Theo_nganh');
            if (! $main || ! $summary || ! $details) {
                throw new RuntimeException('Tệp phải có các sheet Mau_Nhap_Lieu, QM_Tong_hop và QM_Theo_nganh.');
            }
            foreach (['A1' => 'Tên cơ sở trong nguồn', 'B1' => 'Tên cơ sở trong mẫu nhập', 'C1' => 'Dòng mẫu nhập', 'F1' => 'Tổng quy mô nguồn'] as $cell => $header) {
                if ((string) $summary->getCell($cell)->getValue() !== $header) {
                    throw new RuntimeException('Sai cấu trúc sheet QM_Tong_hop tại '.$cell);
                }
            }
            foreach (['D1' => 'Tên ngành chuẩn hóa', 'G1' => 'Tổng quy mô nguồn (gộp)', 'H1' => 'Cao đẳng nguồn (gộp)', 'I1' => 'Trung cấp nguồn (gộp)', 'J1' => 'Sơ cấp và khác nguồn (gộp)'] as $cell => $header) {
                if ((string) $details->getCell($cell)->getValue() !== $header) {
                    throw new RuntimeException('Sai cấu trúc sheet QM_Theo_nganh tại '.$cell);
                }
            }
            $schools = DB::table('schools')->get(['id', 'name', 'code']);
            $byName = $schools->groupBy(fn ($school) => $this->key($school->name));
            $groups = [];
            $usedSchools = [];
            for ($row = 2; $row <= $summary->getHighestDataRow(); $row++) {
                $name = $this->text($summary, 'B', $row);
                if ($name === '' && $this->text($summary, 'A', $row) === '') {
                    continue;
                }
                $mainRow = $this->number($summary->getCell('C'.$row)->getValue(), "QM_Tong_hop!C{$row}");
                if ($mainRow === null || $mainRow < 4 || $this->text($main, 'A', $mainRow) !== $name) {
                    throw new RuntimeException("QM_Tong_hop dòng {$row}: tên cơ sở không khớp dòng tham chiếu trong mẫu nhập.");
                }
                $matches = $byName->get($this->key($name), collect());
                if ($matches->count() !== 1) {
                    throw new RuntimeException("QM_Tong_hop dòng {$row}: không tìm thấy duy nhất cơ sở {$name} trong CSDL.");
                }
                $school = $matches->first();
                if (isset($usedSchools[$school->id]) || isset($groups[$mainRow])) {
                    throw new RuntimeException("QM_Tong_hop dòng {$row}: cơ sở hoặc dòng tham chiếu bị trùng.");
                }
                $usedSchools[$school->id] = true;
                $existing = SchoolSourceRecord::where('school_id', $school->id)->where('record_type', 'training_report')->get();
                if ($existing->count() > 1) {
                    throw new RuntimeException('Cơ sở có nhiều báo cáo đào tạo, cần đối chiếu trước: '.$name);
                }
                $groups[$mainRow] = [
                    'school' => $school, 'source_name' => $this->text($summary, 'A', $row),
                    'source_total' => $this->scale($summary, $row, ['F', 'G', 'H', 'I']),
                    'demo_fields' => $this->demoFields($summary, $row),
                    'existing' => $existing->first(), 'programs' => [], 'program_keys' => [],
                ];
            }
            for ($row = 2; $row <= $details->getHighestDataRow(); $row++) {
                $name = $this->text($details, 'D', $row);
                if ($name === '' && $this->text($details, 'B', $row) === '') {
                    continue;
                }
                $mainRow = $this->number($details->getCell('C'.$row)->getValue(), "QM_Theo_nganh!C{$row}");
                if (! isset($groups[$mainRow]) || $this->key($groups[$mainRow]['school']->name) !== $this->key($this->text($details, 'B', $row)) || $name === '') {
                    throw new RuntimeException("QM_Theo_nganh dòng {$row}: cơ sở hoặc tên ngành không khớp.");
                }
                $key = $this->key($name);
                if (isset($groups[$mainRow]['program_keys'][$key])) {
                    throw new RuntimeException("QM_Theo_nganh dòng {$row}: tên ngành bị trùng trong cùng cơ sở.");
                }
                $groups[$mainRow]['program_keys'][$key] = true;
                $groups[$mainRow]['programs'][] = [
                    'name' => $name, 'current_scale' => $this->scale($details, $row, ['G', 'H', 'I', 'J']),
                    'source_rows' => array_map('intval', preg_split('/[,\s]+/', $this->text($details, 'F', $row), flags: PREG_SPLIT_NO_EMPTY)),
                    'demo_fields' => $this->demoFields($details, $row),
                ];
            }
            if ($groups === []) {
                throw new RuntimeException('Tệp không có dữ liệu quy mô.');
            }
            $plans = [];
            foreach ($groups as $group) {
                if ($group['programs'] === []) {
                    throw new RuntimeException('Thiếu ngành trong QM_Theo_nganh: '.$group['school']->name);
                }
                $existing = $group['existing'];
                $payload = $existing?->payload ?? [];
                $oldPrograms = collect($payload['programs'] ?? [])->keyBy(fn ($program) => $this->key($program['name']));
                $programs = [];
                foreach ($group['programs'] as $program) {
                    // Preserve codes, planned figures and other existing fields.
                    $old = $oldPrograms->get($this->key($program['name']), []);
                    $programs[] = array_replace($old, $program);
                }
                $sums = [];
                foreach (['clean_total', 'dai_hoc', 'cao_dang', 'trung_cap', 'so_cap_va_khac', 'gdtx', 'unallocated'] as $field) {
                    $values = array_column(array_column($programs, 'current_scale'), $field);
                    $known = array_filter($values, fn ($v) => $v !== null);
                    $sums[$field] = $known === [] || ($field === 'clean_total' && count($known) !== count($values)) ? null : array_sum($known);
                }
                $payload['name'] = $group['source_name'];
                $payload['programs'] = $programs;
                $payload['summary']['current_scale'] = [
                    'source_total_row' => $group['source_total'], 'computed_from_clean_programs' => $sums,
                    'demo_fields' => $group['demo_fields'],
                ];
                $payload['source_metadata']['training_scale_excel'] = 1;
                $payload['source_metadata']['contains_demo_figures'] = $group['demo_fields'] !== []
                    || collect($programs)->contains(fn ($program) => $program['demo_fields'] !== []);
                $hash = hash('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
                $result['programs'] += count($programs);
                if ($existing && $existing->content_hash === $hash) {
                    $result['unchanged']++;

                    continue;
                }
                $result[$existing ? 'updated' : 'created']++;
                $plans[] = [
                    'source_key' => $existing?->source_key ?? 'training_scale:'.hash('sha256', $group['school']->id),
                    'school_id' => $group['school']->id, 'record_type' => 'training_report', 'match_status' => 'matched',
                    'source_name' => $group['source_name'], 'content_hash' => $hash, 'payload' => $payload,
                ];
            }
            if (! $dryRun && $plans !== []) {
                DB::transaction(function () use ($plans, &$result): void {
                    // The backup contains only affected reports, never accounts or contacts.
                    $old = SchoolSourceRecord::whereIn('school_id', array_column($plans, 'school_id'))
                        ->where('record_type', 'training_report')->lockForUpdate()->get();
                    $backup = storage_path('app/import-backups/training-scale-'.now()->format('Ymd-His-u').'.json');
                    File::ensureDirectoryExists(dirname($backup));
                    $json = json_encode(['records' => $old, 'target_school_ids' => array_column($plans, 'school_id')], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
                    if (File::put($backup, $json) !== strlen($json)) {
                        throw new RuntimeException('Không tạo được bản sao trước khi nhập quy mô.');
                    }
                    foreach ($plans as $plan) {
                        SchoolSourceRecord::updateOrCreate(['source_key' => $plan['source_key']], $plan);
                    }
                    $result['backup'] = $backup;
                });
            }
            $result['success'] = true;
        } catch (Throwable $error) {
            $result['errors'][] = $error->getMessage();
        }

        return $result;
    }

    private function scale(Worksheet $sheet, int $row, array $columns): array
    {
        $values = [];
        foreach ($columns as $column) {
            $values[] = $this->number($sheet->getCell($column.$row)->getValue(), $sheet->getTitle().'!'.$column.$row);
        }
        [$total, $college, $intermediate, $other] = $values;
        $extras = [];
        foreach (['Đại học' => 'dai_hoc', 'GDTX - GDNN' => 'gdtx'] as $header => $field) {
            if ($column = $this->columnFor($sheet, $header)) {
                $extras[$field] = $this->number($sheet->getCell($column.$row)->getValue(), $sheet->getTitle().'!'.$column.$row);
            }
        }
        $known = array_filter([$college, $intermediate, $other, ...array_values($extras)], fn ($v) => $v !== null);
        $sum = $known === [] ? null : array_sum($known);
        $conflict = $total !== null && $sum !== null && $total < $sum;
        $cleanTotal = $conflict ? null : ($total ?? $sum);

        return [
            'reported_total' => $total, 'clean_total' => $cleanTotal,
            'cao_dang' => $college, 'trung_cap' => $intermediate, 'so_cap_va_khac' => $other,
            ...$extras,
            'unallocated' => $conflict ? null : ($total !== null ? max(0, $total - ($sum ?? 0)) : 0),
            'issues' => $conflict ? ['Tổng nguồn nhỏ hơn tổng các trình độ; cần xác minh.'] : [],
        ];
    }

    private function columnFor(Worksheet $sheet, string $header): ?string
    {
        foreach ($sheet->getRowIterator(1, 1) as $row) {
            foreach ($row->getCellIterator() as $cell) {
                if ((string) $cell->getValue() === $header) {
                    return $cell->getColumn();
                }
            }
        }

        return null;
    }

    private function demoFields(Worksheet $sheet, int $row): array
    {
        $column = $this->columnFor($sheet, 'Trường số giả lập');
        $fields = $column ? preg_split('/[,;\s]+/', $this->text($sheet, $column, $row), flags: PREG_SPLIT_NO_EMPTY) : [];
        $allowed = ['reported_total', 'dai_hoc', 'cao_dang', 'trung_cap', 'so_cap_va_khac', 'gdtx'];
        if (array_diff($fields, $allowed)) {
            throw new RuntimeException('Tên trường số giả lập không hợp lệ.');
        }

        return array_values(array_unique($fields));
    }

    private function number(mixed $value, string $ref): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (! is_numeric($value) || (float) $value < 0 || (float) $value > 1000000000 || floor((float) $value) !== (float) $value) {
            throw new RuntimeException($ref.': cần số nguyên không âm hoặc ô trống; không nhận công thức.');
        }

        return (int) $value;
    }

    private function text(Worksheet $sheet, string $column, int $row): string
    {
        return trim((string) $sheet->getCell($column.$row)->getValue());
    }

    private function key(string $value): string
    {
        $value = str_replace(['hoà', 'hoá', 'hoạ', 'thuỷ'], ['hòa', 'hóa', 'họa', 'thủy'], mb_strtolower($value));

        return Str::squish(preg_replace('/[^\p{L}\p{N}]+/u', ' ', mb_strtolower($value)));
    }
}
