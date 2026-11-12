<?php

namespace App\Console\Commands;

use App\Models\Employer;
use App\Models\JobPosting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportLegacyPublicDirectory extends Command
{
    protected $signature = 'directory:import-legacy {--dry-run : Chỉ thống kê, chưa ghi dữ liệu}';

    protected $description = 'Chuyển danh mục doanh nghiệp và tin việc làm tĩnh sang CSDL; không ghi đè bản ghi đã chuyển';

    public function handle(): int
    {
        $data = json_decode(file_get_contents(database_path('data/legacy-public-directory.json')), true, 512, JSON_THROW_ON_ERROR);
        $this->table(['Doanh nghiệp nguồn', 'Tin việc làm nguồn'], [[count($data['employers']), count($data['jobs'])]]);
        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }
        DB::transaction(function () use ($data): void {
            $index = [];
            foreach ($data['employers'] as $source) {
                $publicId = (string) $source['id'];
                $matches = Employer::query()->where('public_id', $publicId)->get();
                if ($matches->isEmpty() && ! empty($source['tax_code'])) {
                    $matches = Employer::query()->where('tax_code', $source['tax_code'])->get();
                }
                if ($matches->isEmpty()) {
                    $matches = Employer::all()->filter(fn ($e) => $this->key($e->name) === $this->key($source['name']));
                }
                if ($matches->count() > 1) {
                    throw new \RuntimeException('Trùng doanh nghiệp cần đối chiếu: '.$source['name']);
                }
                $employer = $matches->first() ?? new Employer;
                if (! $employer->exists) {
                    $employer->fill(collect($source)->only(['name', 'tax_code', 'employee_count', 'address', 'website', 'phone', 'email', 'is_verified'])->all());
                    $employer->industry = $source['industry_name'] ?? null;
                    $employer->region = $source['region_name'] ?? null;
                    $employer->status = 'ACTIVE';
                    $employer->source = 'LEGACY_PUBLIC';
                }
                // Repeat runs preserve edits made through the admin portal.
                if (! $employer->public_id) {
                    $employer->public_id = $publicId;
                }
                if (! $employer->public_profile) {
                    $employer->public_profile = collect($source)->except(['id', 'jobs_count'])->all();
                }
                $employer->save();
                $index[$this->key($source['name'])] = $employer->id;
            }
            foreach ($data['jobs'] as $source) {
                JobPosting::query()->firstOrCreate(['id' => $source['id']], [
                    'employer_id' => $index[$this->key($source['company'])],
                    'title' => $source['title'], 'industry' => $source['industry'],
                    'recruitment_count' => $source['recruitment_count'],
                    'deadline' => empty($source['deadline']) ? null : Carbon::createFromFormat('!d/m/Y', $source['deadline'])->toDateString(),
                    'status' => 'PUBLISHED', 'details' => $source,
                ]);
            }
        });
        $this->info('Đã chuyển dữ liệu vào CSDL. Có thể chạy lại mà không nhân đôi hoặc ghi đè tin đã chuyển.');

        return self::SUCCESS;
    }

    private function key(string $value): string
    {
        $value = Str::of($value)->ascii()->lower()->value();
        $value = preg_replace(['/\bcong ty trach nhiem huu han\b/', '/\bcong ty co phan\b/', '/\bcty\b/'], ['cong ty tnhh', 'cong ty cp', 'cong ty'], $value);

        return trim(preg_replace('/[^a-z0-9]+/', ' ', $value));
    }
}
