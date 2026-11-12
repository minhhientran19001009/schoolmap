<?php

namespace App\Console\Commands;

use App\Services\CleanGdnnImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportCleanGdnnData extends Command
{
    protected $signature = 'schools:import-clean-data {--apply : Sao lưu và ghi dữ liệu đã đối chiếu}';

    protected $description = 'Nhập dữ liệu GDNN đã làm sạch và chuyển báo cáo tuyển sinh sang CSDL';

    public function handle(CleanGdnnImportService $service): int
    {
        try {
            $clean = json_decode(File::get(database_path('data/gdnn_map_clean_2026_11_10.json')), true, flags: JSON_THROW_ON_ERROR);
            $admissions = json_decode(File::get(base_path('../src/data/admissionResults.json')), true, flags: JSON_THROW_ON_ERROR);
            $plan = $service->plan($clean, $admissions);
            $this->table(['Loại', 'Cơ sở', 'Ghép CSDL', 'Trạng thái'], collect($plan['records'])->map(fn ($r) => [$r['record_type'], $r['source_name'], $r['school_id'] ?: '—', $r['match_status']])->all());
            $this->line(json_encode($plan['counts'], JSON_UNESCAPED_UNICODE));
            foreach ($plan['skipped'] as $item) {
                $this->warn('Bỏ qua: '.$item['source_name'].' — '.$item['reason']);
            }
            if (! $this->option('apply')) {
                $this->info('Đối chiếu hoàn tất, chưa ghi dữ liệu.');

                return self::SUCCESS;
            }
            $backup = $service->apply($plan);
            $this->info('Đã nhập dữ liệu. Bản sao: '.$backup);

            return self::SUCCESS;
        } catch (\Throwable $error) {
            $this->error($error->getMessage());

            return self::FAILURE;
        }
    }
}
