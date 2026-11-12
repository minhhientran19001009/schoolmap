<?php

namespace App\Filament\Resources\WorkforceDemands\Pages;

use App\Filament\Resources\WorkforceDemands\WorkforceDemandResource;
use App\Services\WorkforceDemandExcelService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class ListWorkforceDemands extends ListRecords
{
    protected static string $resource = WorkforceDemandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')->label('Tải khung Excel mẫu')->icon(Heroicon::OutlinedArrowDownTray)->color('success')
                ->action(fn () => app(WorkforceDemandExcelService::class)->downloadTemplate()),
            Action::make('exportDemands')->label('Xuất danh sách ra Excel')->icon(Heroicon::OutlinedArrowDownTray)->color('warning')
                ->action(fn () => app(WorkforceDemandExcelService::class)->exportCurrentDemands()),
            Action::make('importExcel')->label('Nhập từ Excel')->icon(Heroicon::OutlinedArrowUpTray)->color('info')
                ->formWrapper(false)->modalHeading('Nhập nhu cầu nhân lực / việc làm')
                ->modalDescription('Nhập nhu cầu tuyển dụng tổng hợp theo doanh nghiệp, ngành/nghề, trình độ và kỳ báo cáo.')
                ->modalWidth(Width::Large)->modalContent(view('filament.resources.workforce-demands.import-excel'))
                ->modalSubmitAction(fn (Action $action): Action => $action->label('Kiểm tra / nhập dữ liệu')->icon(Heroicon::OutlinedArrowUpTray)->submit('workforce-demand-excel-import-form')->formId('workforce-demand-excel-import-form'))
                ->modalCancelActionLabel('Hủy')->modalFooterActionsAlignment(Alignment::End),
            CreateAction::make()->label('Thêm nhu cầu'),
        ];
    }
}
