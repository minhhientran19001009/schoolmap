<?php

require __DIR__.'/../backend/vendor/autoload.php';
$app = require __DIR__.'/../backend/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (! $app->environment('local') || ! in_array(config('database.connections.mysql.host'), ['127.0.0.1', 'localhost'], true)) {
    fwrite(STDERR, "Chỉ chạy công cụ này với môi trường local và CSDL local.\n");
    exit(1);
}
$path = realpath($argv[1] ?? '');
if (! $path || ! is_file($path)) {
    fwrite(STDERR, "Cần đường dẫn file Excel quy mô.\n");
    exit(1);
}
$result = $app->make(\App\Services\TrainingScaleExcelService::class)->importFromFile($path, ! in_array('--apply', $argv, true));
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT).PHP_EOL;
exit($result['success'] ? 0 : 1);
