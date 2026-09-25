<?php

namespace App\Filament\School\Resources\ManagedSchools\Pages;

use App\Filament\School\Resources\ManagedSchools\ManagedSchoolResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListManagedSchools extends ListRecords
{
    protected static string $resource = ManagedSchoolResource::class;

    public function getTitle(): string
    {
        return 'Thông tin trường và cơ sở trực thuộc';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Thêm cơ sở trực thuộc'),
        ];
    }
}
