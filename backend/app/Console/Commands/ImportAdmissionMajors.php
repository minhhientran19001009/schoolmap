<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Models\SchoolTrainingMajor;
use App\Models\TrainingMajor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class ImportAdmissionMajors extends Command
{
    protected $signature = 'majors:import-admission-report
        {--apply : Sao lưu rồi ghi danh mục ngành và liên kết trường vào CSDL}
        {--file= : Đường dẫn JSON kết xuất từ workbook tuyển sinh}';

    protected $description = 'Nhập ngành/nghề và trường đào tạo từ bảng kết quả tuyển sinh, không thay đổi chỉ tiêu hằng năm';

    public function handle(): int
    {
        $path = $this->option('file') ?: base_path('../src/data/admissionResults.json');
        if (! File::isFile($path)) {
            $this->error("Không tìm thấy file: {$path}");

            return self::FAILURE;
        }

        try {
            $source = json_decode(File::get($path), true, flags: JSON_THROW_ON_ERROR);
            $institutions = $source['institutions'] ?? null;
            if (! is_array($institutions) || $institutions === []) {
                throw new RuntimeException('File nguồn không có danh sách cơ sở hợp lệ.');
            }

            // Validate every mapping before any write, including the school code.
            $schools = [];
            foreach ($institutions as $institution) {
                $schoolId = (string) ($institution['school_id'] ?? '');
                $school = School::query()->find($schoolId);
                if (! $school || (string) $school->code !== (string) ($institution['school_code'] ?? '')) {
                    throw new RuntimeException("Không khớp cơ sở trong CSDL: {$schoolId}");
                }
                $schools[$schoolId] = $school;
            }

            $catalog = [];
            $assignments = [];
            $skippedHeadings = 0;
            $sourceRows = 0;
            foreach ($institutions as $institution) {
                $schoolId = (string) $institution['school_id'];
                foreach ($institution['programs'] ?? [] as $program) {
                    $sourceRows++;
                    $name = $this->canonicalName((string) ($program['name'] ?? ''));
                    if ($name === '') {
                        $skippedHeadings++;
                        continue;
                    }

                    $slug = Str::slug($name);
                    $code = trim((string) ($program['code'] ?? ''));
                    $catalog[$slug] ??= ['name' => $name, 'codes' => []];
                    if ($code !== '') {
                        $catalog[$slug]['codes'][$code] = true;
                    }

                    $key = $schoolId.'|'.$slug;
                    $assignments[$key] ??= [
                        'school_id' => $schoolId,
                        'slug' => $slug,
                        'levels' => [],
                    ];
                    foreach (['cao_dang', 'trung_cap', 'so_cap'] as $level) {
                        $value = $program[$level] ?? null;
                        if (is_numeric($value) && (int) $value > 0) {
                            $assignments[$key]['levels'][$level] = true;
                        }
                    }
                    if ($assignments[$key]['levels'] === []) {
                        $prefix = substr($code, 0, 1);
                        if ($prefix === '6') {
                            $assignments[$key]['levels']['cao_dang'] = true;
                        } elseif ($prefix === '5') {
                            $assignments[$key]['levels']['trung_cap'] = true;
                        }
                    }
                }
            }

            // A catalog item has one code field. Leave it empty when source
            // rows use multiple codes for different training levels, or when
            // one code refers to more than one distinct catalog item.
            $slugsByCode = [];
            $invalidCodes = 0;
            foreach ($catalog as $slug => $item) {
                foreach (array_keys($item['codes']) as $code) {
                    $slugsByCode[$code][$slug] = true;
                    if (! preg_match('/^\d{7,8}$/', $code)) {
                        $invalidCodes++;
                    }
                }
            }
            foreach ($catalog as $slug => &$item) {
                $codes = array_keys($item['codes']);
                $item['code'] = count($codes) === 1
                    && preg_match('/^\d{7,8}$/', $codes[0])
                    && count($slugsByCode[$codes[0]]) === 1
                    ? (string) $codes[0]
                    : null;
            }
            unset($item);

            $existingMajors = TrainingMajor::query()->get()->keyBy('slug');
            $existingAssignments = SchoolTrainingMajor::query()
                ->whereIn('school_id', array_keys($schools))
                ->get()
                ->groupBy('school_id');
            $newMajors = 0;
            $newAssignments = 0;
            $newCodes = 0;
            $codeConflicts = 0;
            foreach ($catalog as $slug => $item) {
                $existing = $existingMajors->get($slug);
                if (! $existing) {
                    $newMajors++;
                } elseif ($item['code'] && ! $existing->code) {
                    $newCodes++;
                } elseif ($item['code'] && $existing->code && $existing->code !== $item['code']) {
                    $codeConflicts++;
                }
            }
            foreach ($assignments as $assignment) {
                $major = $existingMajors->get($assignment['slug']);
                $alreadyLinked = $major && $existingAssignments
                    ->get($assignment['school_id'], collect())
                    ->contains('training_major_id', $major->id);
                if (! $alreadyLinked) {
                    $newAssignments++;
                }
            }

            $this->table(['Nội dung', 'Số lượng'], [
                ['Dòng ngành/nghề trong file', $sourceRows],
                ['Dòng tiêu đề bỏ qua', $skippedHeadings],
                ['Ngành/nghề khác nhau sau chuẩn hóa', count($catalog)],
                ['Ngành/nghề sẽ thêm', $newMajors],
                ['Mã ngành có thể bổ sung', $newCodes],
                ['Mã nguồn không đúng định dạng số', $invalidCodes],
                ['Mã mâu thuẫn với CSDL', $codeConflicts],
                ['Liên kết trường–ngành sẽ thêm', $newAssignments],
            ]);
            if (! $this->option('apply')) {
                $this->info('Chế độ xem trước. Dùng --apply để sao lưu và cập nhật CSDL.');

                return self::SUCCESS;
            }

            $backupPath = storage_path('app/import-backups/training-majors-'.now()->format('Ymd-His-u').'.json');
            File::ensureDirectoryExists(dirname($backupPath));
            $backup = json_encode([
                'training_majors' => DB::table('training_majors')->get(),
                'school_training_major' => DB::table('school_training_major')->get(),
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
            if (File::put($backupPath, $backup) !== strlen($backup)) {
                throw new RuntimeException('Không thể sao lưu đầy đủ hai bảng trước khi nhập.');
            }

            DB::transaction(function () use ($catalog, $assignments, $schools): void {
                $majorsBySlug = TrainingMajor::query()->get()->keyBy('slug');
                foreach ($catalog as $slug => $item) {
                    $major = $majorsBySlug->get($slug);
                    if (! $major) {
                        $major = TrainingMajor::create([
                            'name' => $item['name'],
                            'code' => $item['code'],
                            'is_active' => true,
                        ]);
                        $majorsBySlug->put($slug, $major);
                    } elseif ($item['code'] && ! $major->code) {
                        $major->update(['code' => $item['code']]);
                    }
                }

                foreach ($assignments as $assignment) {
                    $major = $majorsBySlug->get($assignment['slug']);
                    // Keep all existing assignments, degree levels and annual quotas.
                    if (SchoolTrainingMajor::query()
                        ->where('school_id', $assignment['school_id'])
                        ->where('training_major_id', $major->id)
                        ->exists()) {
                        continue;
                    }
                    $schoolLevel = (string) $schools[$assignment['school_id']]->education_level_id;
                    $levels = array_keys($assignment['levels']);
                    $level = in_array($schoolLevel, $levels, true)
                        ? $schoolLevel
                        : ($levels[0] ?? 'chua_xac_dinh');
                    SchoolTrainingMajor::create([
                        'school_id' => $assignment['school_id'],
                        'training_major_id' => $major->id,
                        'degree_level' => $level,
                        'annual_quota' => 0,
                    ]);
                }
            });

            $this->info("Đã cập nhật. Bản sao dữ liệu trước khi nhập: {$backupPath}");

            return self::SUCCESS;
        } catch (\Throwable $error) {
            $this->error($error->getMessage());

            return self::FAILURE;
        }
    }

    private function canonicalName(string $name): string
    {
        $name = trim(preg_replace('/\s+/u', ' ', $name) ?? $name);
        if (preg_match('/^(?:hệ\s+(?:cao đẳng|trung cấp|sơ cấp)|sơ cấp)$/iu', $name)) {
            return '';
        }

        $name = preg_replace('/^(?:nghề|cao đẳng)\s+/iu', '', $name) ?? $name;
        $name = preg_replace('/\s*\(TC\)$/iu', '', $name) ?? $name;
        $name = preg_replace('/ôt\s*ô/iu', 'ô tô', $name) ?? $name;
        $name = preg_replace('/ĐHKK/iu', 'điều hòa không khí', $name) ?? $name;
        $name = preg_replace('/Công nghệ KT/iu', 'Công nghệ kỹ thuật', $name) ?? $name;
        $name = preg_replace('/Công nghệ, kỹ thuật/iu', 'Công nghệ kỹ thuật', $name) ?? $name;

        return trim($name);
    }
}
