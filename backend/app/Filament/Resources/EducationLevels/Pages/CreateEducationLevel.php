<?php

namespace App\Filament\Resources\EducationLevels\Pages;

use App\Filament\Resources\EducationLevels\EducationLevelResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateEducationLevel extends CreateRecord
{
    protected static string $resource = EducationLevelResource::class;

    protected Width | string | null $maxWidth = Width::Full;

    public function getTitle(): string
    {
        return 'Thêm Cấp học / Danh mục mới';
    }
}
