<?php

namespace App\Filament\Resources\Employers\Pages;

use App\Filament\Resources\Employers\EmployerResource;
use App\Services\EmployerExcelService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class ListEmployers extends ListRecords
{
    protected static string $resource = EmployerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')->label('Tải khung Excel mẫu')->icon(Heroicon::OutlinedArrowDownTray)->color('success')
                ->action(fn () => app(EmployerExcelService::class)->downloadTemplate()),
            Action::make('exportEmployers')->label('Xuất danh sách ra Excel')->icon(Heroicon::OutlinedArrowDownTray)->color('warning')
                ->action(fn () => app(EmployerExcelService::class)->exportCurrentEmployers()),
            Action::make('importExcel')->label('Nhập từ Excel')->icon(Heroicon::OutlinedArrowUpTray)->color('info')
                ->formWrapper(false)->modalHeading('Nhập danh sách doanh nghiệp')
                ->modalDescription('Có thể kiểm tra tệp trước khi chính thức ghi dữ liệu.')
                ->modalWidth(Width::Large)->modalContent(view('filament.resources.employers.import-excel'))
                ->modalSubmitAction(fn (Action $action): Action => $action->label('Kiểm tra / nhập dữ liệu')->icon(Heroicon::OutlinedArrowUpTray)->submit('employer-excel-import-form')->formId('employer-excel-import-form'))
                ->modalCancelActionLabel('Hủy')->modalFooterActionsAlignment(Alignment::End),
            CreateAction::make()->label('Thêm doanh nghiệp'),
        ];
    }
}
