<?php

namespace App\Filament\Resources\TrainingMajors\Pages;

use App\Filament\Resources\TrainingMajors\TrainingMajorResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateTrainingMajor extends CreateRecord
{
    protected static string $resource = TrainingMajorResource::class;

    protected Width|string|null $maxWidth = Width::Full;

    public function getTitle(): string
    {
        return 'Thêm chuyên ngành đào tạo';
    }
}
