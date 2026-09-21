<?php

namespace App\Filament\Resources\TrainingMajors\Pages;

use App\Filament\Resources\TrainingMajors\TrainingMajorResource;
use App\Services\TrainingMajorExcelService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class ListTrainingMajors extends ListRecords
{
    protected static string $resource = TrainingMajorResource::class;

    public function getTitle(): string
    {
        return 'Danh mục Chuyên ngành đào tạo';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')
                ->label('Tải khung Excel mẫu')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('success')
                ->action(fn () => app(TrainingMajorExcelService::class)->downloadTemplate()),

            Action::make('exportMajors')
                ->label('Xuất danh sách ra Excel')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('warning')
                ->action(fn () => app(TrainingMajorExcelService::class)->exportCurrentMajors()),

            Action::make('importExcel')
                ->label('Nhập từ Excel')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->color('info')
                ->formWrapper(false)
                ->modalHeading('Nhập danh mục chuyên ngành')
                ->modalDescription('Tải tệp Excel theo khung mẫu để thêm mới hoặc cập nhật tên và trạng thái chuyên ngành.')
                ->modalWidth(Width::Large)
                ->modalContent(view('filament.resources.training-majors.import-excel'))
                ->modalSubmitAction(fn (Action $action): Action => $action
                    ->label('Tải lên và nhập dữ liệu')
                    ->icon(Heroicon::OutlinedArrowUpTray)
                    ->submit('training-major-excel-import-form')
                    ->formId('training-major-excel-import-form'))
                ->modalCancelActionLabel('Hủy')
                ->modalFooterActionsAlignment(Alignment::End),

            CreateAction::make()->label('Thêm chuyên ngành'),
        ];
    }
}
