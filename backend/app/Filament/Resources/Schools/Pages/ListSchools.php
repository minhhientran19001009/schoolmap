<?php

namespace App\Filament\Resources\Schools\Pages;

use App\Filament\Resources\Schools\SchoolResource;
use App\Services\SchoolExcelService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class ListSchools extends ListRecords
{
    protected static string $resource = SchoolResource::class;

    public function getTitle(): string
    {
        return 'Danh sách Trường học';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')
                ->label('Tải khung Excel mẫu')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('success')
                ->action(function () {
                    return app(SchoolExcelService::class)->downloadTemplate();
                }),

            Action::make('exportSchools')
                ->label('Xuất danh sách ra Excel')
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('warning')
                ->action(function () {
                    return app(SchoolExcelService::class)->exportCurrentSchools();
                }),

            Action::make('importExcel')
                ->label('Nhập từ Excel')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->color('info')
                ->formWrapper(false)
                ->modalHeading('Nhập từ Excel')
                ->modalDescription('Chọn tệp Excel theo khung mẫu để tạo mới hoặc cập nhật danh sách trường học.')
                ->modalWidth(Width::Large)
                ->modalContent(view('filament.resources.schools.import-excel'))
                ->modalSubmitAction(fn (Action $action): Action => $action
                    ->label('Tải lên và nhập dữ liệu')
                    ->icon(Heroicon::OutlinedArrowUpTray)
                    ->submit('school-excel-import-form')
                    ->formId('school-excel-import-form'))
                ->modalCancelActionLabel('Hủy')
                ->modalFooterActionsAlignment(Alignment::End),

            Action::make('importTrainingScale')
                ->label('Nhập quy mô đào tạo')
                ->icon(Heroicon::OutlinedChartBar)
                ->color('info')
                ->formWrapper(false)
                ->modalHeading('Nhập quy mô đào tạo từ Excel')
                ->modalDescription('Tải file có các sheet QM_Tong_hop và QM_Theo_nganh. Chỉ cập nhật báo cáo quy mô; giữ nguyên chỉ tiêu, mã ngành và thông tin trường.')
                ->modalWidth(Width::Large)
                ->modalContent(view('filament.resources.schools.import-excel', ['trainingScale' => true]))
                ->modalSubmitAction(fn (Action $action): Action => $action
                    ->label('Kiểm tra / nhập quy mô')
                    ->submit('school-scale-excel-import-form')
                    ->formId('school-scale-excel-import-form'))
                ->modalCancelActionLabel('Hủy'),

            CreateAction::make()->label('Thêm Trường học mới'),
        ];
    }
}
