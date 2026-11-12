<?php

namespace App\Console\Commands;

use App\Services\SchoolDeduplicationService;
use Illuminate\Console\Command;

class DeduplicateSchools extends Command
{
    protected $signature = 'schools:deduplicate {--apply : Sao lưu và gộp theo ánh xạ đã đối chiếu}';

    protected $description = 'Loại bản ghi seed trùng, giữ dữ liệu mới và các liên kết nghiệp vụ';

    public function handle(SchoolDeduplicationService $service): int
    {
        try {
            $pairs = $service->plan();
            $this->table(['ID dư', 'ID giữ lại', 'Cơ sở'], array_map(fn ($p) => [$p['old_id'], $p['target_id'], $p['name']], $pairs));
            if (! $this->option('apply')) {
                $this->info(count($pairs).' cặp đã đối chiếu. Chưa thay đổi dữ liệu.');

                return self::SUCCESS;
            }
            $result = $service->apply();
            $this->info(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

            return self::SUCCESS;
        } catch (\Throwable $error) {
            $this->error($error->getMessage());

            return self::FAILURE;
        }
    }
}
