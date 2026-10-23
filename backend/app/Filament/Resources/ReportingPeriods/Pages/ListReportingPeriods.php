<?php

namespace App\Filament\Resources\ReportingPeriods\Pages;

use App\Filament\Resources\ReportingPeriods\ReportingPeriodResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReportingPeriods extends ListRecords
{
    protected static string $resource = ReportingPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Thêm kỳ báo cáo')];
    }
}
