<?php

namespace App\Filament\Resources\TrainingMajors\Pages;

use App\Filament\Resources\TrainingMajors\TrainingMajorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

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
            CreateAction::make()->label('Thêm chuyên ngành'),
        ];
    }
}
