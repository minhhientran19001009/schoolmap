<?php

namespace App\Filament\Resources\Schools\Pages;

use App\Filament\Resources\Schools\SchoolResource;
use App\Services\SchoolExcelService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
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
                ->form([
                    \Filament\Forms\Components\FileUpload::make('excel_file')
                        ->label('Chọn tệp Excel (.xlsx, .xls)')
                        ->helperText('Tải lên tệp Excel theo khung mẫu. Nếu thiếu tọa độ hoặc số liệu, hệ thống sẽ tự động gán giá trị an toàn.')
                        ->disk('local')
                        ->directory('temp_imports')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $paths = [
                        storage_path('app/private/' . $data['excel_file']),
                        storage_path('app/' . $data['excel_file']),
                    ];

                    $filePath = null;
                    foreach ($paths as $p) {
                        if (file_exists($p)) {
                            $filePath = $p;
                            break;
                        }
                    }

                    if (!$filePath) {
                        \Filament\Notifications\Notification::make()
                            ->title('Không tìm thấy tệp tải lên')
                            ->danger()
                            ->send();
                        return;
                    }

                    $service = app(SchoolExcelService::class);
                    $res = $service->importFromFile($filePath);

                    @unlink($filePath);

                    if ($res['success']) {
                        $msg = "Thành công: Tạo mới {$res['created']} trường, Cập nhật {$res['updated']} trường.";
                        if ($res['skipped'] > 0) {
                            $msg .= " Bỏ qua {$res['skipped']} dòng trống.";
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Nhập dữ liệu Excel thành công!')
                            ->body($msg)
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Lỗi khi đọc file Excel')
                            ->body($res['message'] ?? 'Không thể xử lý file.')
                            ->danger()
                            ->send();
                    }
                }),

            CreateAction::make()->label('Thêm Trường học mới'),
        ];
    }
}
