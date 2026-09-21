<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Models\TrainingMajor;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BackfillTrainingMajors extends Command
{
    protected $signature = 'majors:backfill-from-json {--dry-run : Only report changes without writing them}';

    protected $description = 'Chuyển dữ liệu ngành đào tạo JSON cũ sang danh mục và bảng liên kết mới';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $majorCount = 0;
        $assignmentCount = 0;

        School::query()->select(['id', 'training_majors'])->chunkById(100, function ($schools) use (&$majorCount, &$assignmentCount, $dryRun): void {
            foreach ($schools as $school) {
                foreach ($school->training_majors as $item) {
                    $name = trim((string) ($item['name'] ?? ''));
                    if ($name === '') {
                        continue;
                    }

                    $degreeLevel = trim((string) ($item['degree_level'] ?? 'cao_dang')) ?: 'cao_dang';
                    $annualQuota = max(0, (int) ($item['annual_quota'] ?? 0));
                    $slug = Str::slug($name);
                    $major = TrainingMajor::query()->where('slug', $slug)->first();

                    if (! $major) {
                        $majorCount++;
                        if (! $dryRun) {
                            $major = TrainingMajor::create([
                                'name' => $name,
                                'slug' => $slug,
                                'is_active' => true,
                            ]);
                        }
                    }

                    if (! $dryRun && $major) {
                        $assignment = $school->majorAssignments()
                            ->where('training_major_id', $major->id)
                            ->where('degree_level', $degreeLevel)
                            ->first();

                        if (! $assignment) {
                            $school->majorAssignments()->create([
                                'training_major_id' => $major->id,
                                'degree_level' => $degreeLevel,
                                'annual_quota' => $annualQuota,
                            ]);
                            $assignmentCount++;
                        } elseif ((int) $assignment->annual_quota !== $annualQuota) {
                            $assignment->update(['annual_quota' => $annualQuota]);
                        }
                    } elseif ($dryRun) {
                        $assignmentCount++;
                    }
                }
            }
        }, 'id');

        $mode = $dryRun ? 'Xem trước' : 'Đã cập nhật';
        $this->info("{$mode}: {$majorCount} chuyên ngành mới, {$assignmentCount} liên kết trường - chuyên ngành.");

        return self::SUCCESS;
    }
}
