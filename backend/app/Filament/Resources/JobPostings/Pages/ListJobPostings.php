<?php

namespace App\Filament\Resources\JobPostings\Pages;

use App\Filament\Resources\JobPostings\JobPostingResource;
use App\Services\JobPostingExcelService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class ListJobPostings extends ListRecords
{
    protected static string $resource = JobPostingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')->label('Tải khung Excel mẫu')->icon(Heroicon::OutlinedArrowDownTray)->color('success')->action(fn () => app(JobPostingExcelService::class)->downloadTemplate()),
            Action::make('exportJobs')->label('Xuất Excel')->icon(Heroicon::OutlinedArrowDownTray)->color('warning')->action(fn () => app(JobPostingExcelService::class)->exportCurrentJobs()),
            Action::make('importExcel')->label('Nhập từ Excel')->icon(Heroicon::OutlinedArrowUpTray)->color('info')->formWrapper(false)
                ->modalHeading('Nhập tin việc làm')->modalWidth(Width::Large)->modalContent(view('filament.resources.job-postings.import-excel'))
                ->modalSubmitAction(fn (Action $action): Action => $action->label('Kiểm tra / nhập dữ liệu')->submit('job-posting-excel-import-form')->formId('job-posting-excel-import-form'))
                ->modalCancelActionLabel('Hủy')->modalFooterActionsAlignment(Alignment::End),
            CreateAction::make()->label('Thêm tin việc làm'),
        ];
    }
}
