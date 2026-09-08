<?php

namespace App\Filament\Resources\Wards\Pages;

use App\Filament\Resources\Wards\WardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWards extends ListRecords
{
    protected static string $resource = WardResource::class;

    public function getTitle(): string
    {
        return 'Danh sách Phường / Xã';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Thêm Phường / Xã mới'),
        ];
    }
}
