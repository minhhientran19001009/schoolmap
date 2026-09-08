<?php

namespace App\Filament\Resources\Schools\Pages;

use App\Filament\Resources\Schools\SchoolResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

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
            CreateAction::make()->label('Thêm Trường học mới'),
        ];
    }
}
