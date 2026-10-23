<?php

namespace App\Console\Commands;

use App\Services\AppsScriptPortalImportService;
use Illuminate\Console\Command;
use JsonException;

class ImportAppsScriptPortal extends Command
{
    protected $signature = 'supply-demand:import-apps-script
        {file : Đường dẫn tệp JSON xuất từ /api/portal của Apps Script}
        {--include-samples : Nhập cả dữ liệu minh họa}
        {--publish : Công bố nhu cầu/năng lực ngay sau khi nhập}
        {--dry-run : Kiểm tra và hoàn tác toàn bộ thay đổi}';

    protected $description = 'Chuyển dữ liệu cung-cầu từ cổng Google Apps Script vào hệ thống web';

    public function handle(AppsScriptPortalImportService $service): int
    {
        $path = (string) $this->argument('file');
        $path = is_file($path) ? $path : base_path($path);

        if (! is_file($path) || ! is_readable($path)) {
            $this->error("Không đọc được tệp: {$path}");

            return self::FAILURE;
        }

        try {
            $payload = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $this->error('Tệp JSON không hợp lệ: '.$exception->getMessage());

            return self::FAILURE;
        }

        if (! is_array($payload)) {
            $this->error('Tệp phải chứa một đối tượng JSON.');

            return self::FAILURE;
        }

        $result = $service->import(
            $payload,
            (bool) $this->option('include-samples'),
            (bool) $this->option('publish'),
            (bool) $this->option('dry-run'),
        );

        $this->table(['Loại dữ liệu', 'Số bản ghi'], [
            ['Doanh nghiệp', $result['employers']],
            ['Nhu cầu', $result['demands']],
            ['Năng lực', $result['capacities']],
            ['Đề xuất', $result['proposals']],
            ['Bỏ qua', $result['skipped']],
            ['Lỗi', count($result['errors'])],
        ]);

        if ($this->option('dry-run')) {
            $this->warn('Chế độ kiểm tra: cơ sở dữ liệu không bị thay đổi.');
        } elseif (! $this->option('publish')) {
            $this->info('Nhu cầu và năng lực đã nhập ở trạng thái chờ rà soát; hồ sơ doanh nghiệp đã được đưa vào danh bạ.');
        }

        foreach (array_slice($result['errors'], 0, 30) as $error) {
            $this->warn('- '.$error);
        }

        if (count($result['errors']) > 30) {
            $this->warn('Còn '.(count($result['errors']) - 30).' lỗi chưa hiển thị.');
        }

        return count($result['errors']) > 0 ? self::FAILURE : self::SUCCESS;
    }
}
