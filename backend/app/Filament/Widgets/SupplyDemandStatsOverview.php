<?php

namespace App\Filament\Widgets;

use App\Services\SupplyDemandStatisticsService;
use Filament\Widgets\Widget;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SupplyDemandStatsOverview extends Widget
{
    protected static ?int $sort = 2;

    protected string $view = 'filament.widgets.supply-demand-stats-overview';

    protected int|string|array $columnSpan = 'full';

    public int|string|null $periodId = null;

    public function mount(SupplyDemandStatisticsService $statistics): void
    {
        if (! filled($this->periodId)) {
            $this->periodId = $statistics->defaultPeriod()?->getKey();
        }
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $statistics = app(SupplyDemandStatisticsService::class);

        return [
            'periodOptions' => $statistics->periodOptions(),
            'statistics' => $statistics->build($this->periodId),
        ];
    }

    public function exportSummary(): StreamedResponse
    {
        $statistics = app(SupplyDemandStatisticsService::class)->build($this->periodId);
        $period = $statistics['period'];
        $filename = 'thong-ke-cung-cau-'.($period?->code ?? 'tong-hop').'.csv';

        return response()->streamDownload(function () use ($statistics): void {
            $stream = fopen('php://output', 'wb');
            fwrite($stream, "\xEF\xBB\xBF");
            fputcsv($stream, ['Ngành / nghề', 'Trình độ', 'Nhu cầu', 'Năng lực', 'Thiếu', 'Dư', 'Đã đề xuất']);

            foreach ($statistics['rows'] as $row) {
                fputcsv($stream, [
                    $row['major'],
                    $row['level'],
                    $row['demand'],
                    $row['capacity'],
                    $row['gap'],
                    $row['surplus'],
                    $row['proposed'],
                ]);
            }

            fclose($stream);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
