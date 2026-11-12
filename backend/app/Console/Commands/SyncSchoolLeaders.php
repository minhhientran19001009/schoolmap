<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use JsonException;

class SyncSchoolLeaders extends Command
{
    protected $signature = 'schools:sync-leaders
        {--apply : Ghi thay đổi sau khi xem trước; mặc định chỉ đối chiếu}';

    protected $description = 'Đối chiếu và cập nhật lãnh đạo cơ sở GDNN từ danh sách ngày 05/11/2026';

    public function handle(): int
    {
        $path = database_path('data/school_leaders_2026_11_05.json');

        if (! File::isFile($path)) {
            $this->error('Không tìm thấy danh sách lãnh đạo: '.$path);

            return self::FAILURE;
        }

        try {
            $roster = json_decode((string) File::get($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $this->error('Danh sách lãnh đạo không phải JSON hợp lệ: '.$exception->getMessage());

            return self::FAILURE;
        }

        if (! is_array($roster) || ! array_is_list($roster)) {
            $this->error('Danh sách lãnh đạo phải là một mảng JSON.');

            return self::FAILURE;
        }

        $schools = DB::table('schools')
            ->select('id', 'code', 'name', 'campus_type', 'parent_school_id', 'principal', 'leaders')
            ->get()
            ->filter(fn ($school) => $school->parent_school_id === null && in_array($school->campus_type, [null, 'MAIN'], true))
            ->groupBy(fn ($school) => $this->nameKey($school->name));

        $updates = [];
        $missing = [];
        $ambiguous = [];
        $unchanged = 0;
        $seen = [];

        foreach ($roster as $entry) {
            if (! $this->validEntry($entry)) {
                $this->error('Bản ghi danh sách không hợp lệ: '.json_encode($entry, JSON_UNESCAPED_UNICODE));

                return self::FAILURE;
            }

            $key = $this->nameKey($entry['school']);
            if (isset($seen[$key])) {
                $this->error("Tên cơ sở xuất hiện nhiều lần trong danh sách: {$entry['school']}");

                return self::FAILURE;
            }
            $seen[$key] = true;

            $matches = $schools->get($key, collect());
            if ($matches->isEmpty()) {
                $missing[] = "{$entry['ref']}: {$entry['school']}";
                continue;
            }
            if ($matches->count() !== 1) {
                $ambiguous[] = "{$entry['ref']}: {$entry['school']} ({$matches->count()} cơ sở trùng tên)";
                continue;
            }

            $school = $matches->first();
            $oldLeaders = json_decode((string) $school->leaders, true);
            if ($school->principal === $entry['principal'] && $oldLeaders === $entry['leaders']) {
                $unchanged++;
                continue;
            }

            $updates[] = ['school' => $school, 'entry' => $entry];
        }

        $this->table(['Mã', 'Cơ sở', 'Nguồn', 'Lãnh đạo hiện tại', 'Lãnh đạo mới'], array_map(
            fn ($item) => [
                $item['school']->code,
                $item['school']->name,
                $item['entry']['ref'],
                $item['school']->principal ?: '—',
                $item['entry']['principal'],
            ],
            $updates,
        ));

        $this->info('Dự kiến cập nhật: '.count($updates).'; đã đúng: '.$unchanged.'; không tìm thấy: '.count($missing).'; trùng tên: '.count($ambiguous).'.');
        foreach ($missing as $message) {
            $this->warn('Không tìm thấy, giữ nguyên: '.$message);
        }
        foreach ($ambiguous as $message) {
            $this->warn('Không đủ chắc chắn, giữ nguyên: '.$message);
        }

        if (! $this->option('apply')) {
            $this->comment('Chỉ xem trước. Chạy thêm --apply để ghi dữ liệu sau khi rà soát danh sách trên.');

            return self::SUCCESS;
        }

        if ($updates === []) {
            $this->info('Không có dữ liệu cần thay đổi.');

            return self::SUCCESS;
        }

        // Save the exact previous values before writing, so this operation is reversible.
        $backupDir = storage_path('app/leader-sync-backups');
        File::ensureDirectoryExists($backupDir);
        $backupPath = $backupDir.'/'.now()->format('Ymd_His_u').'.json';
        $backup = array_map(fn ($item) => [
            'id' => $item['school']->id,
            'code' => $item['school']->code,
            'name' => $item['school']->name,
            'principal' => $item['school']->principal,
            'leaders' => $item['school']->leaders,
        ], $updates);

        try {
            $written = File::put($backupPath, json_encode($backup, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR), true);
            if ($written === false) {
                throw new \RuntimeException('Không tạo được bản sao dữ liệu trước thay đổi.');
            }
            DB::transaction(function () use ($updates): void {
                foreach ($updates as $item) {
                    DB::table('schools')->where('id', $item['school']->id)->update([
                        'principal' => $item['entry']['principal'],
                        'leaders' => json_encode($item['entry']['leaders'], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                        'updated_at' => now(),
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            $this->error('Không cập nhật được dữ liệu: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Đã cập nhật '.count($updates).' cơ sở. Bản sao dữ liệu trước thay đổi: '.$backupPath);

        return self::SUCCESS;
    }

    private function nameKey(string $name): string
    {
        return trim((string) preg_replace('/[^a-z0-9]+/', ' ', strtolower(Str::ascii($name))));
    }

    private function validEntry(mixed $entry): bool
    {
        if (! is_array($entry) || ! filled($entry['ref'] ?? null) || ! filled($entry['school'] ?? null) || ! filled($entry['principal'] ?? null)) {
            return false;
        }

        if (! isset($entry['leaders']) || ! is_array($entry['leaders']) || $entry['leaders'] === []) {
            return false;
        }

        foreach ($entry['leaders'] as $leader) {
            if (! is_array($leader) || ! filled($leader['name'] ?? null) || ! filled($leader['position'] ?? null)) {
                return false;
            }
        }

        return true;
    }
}
