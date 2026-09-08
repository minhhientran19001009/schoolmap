<?php

namespace App\Filament\Resources\EducationLevels\Pages;

use App\Filament\Resources\EducationLevels\EducationLevelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEducationLevels extends ListRecords
{
    protected static string $resource = EducationLevelResource::class;

    public function getTitle(): string
    {
        return 'Danh mục Cấp học';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Thêm Cấp học mới'),
        ];
    }
}
