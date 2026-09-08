<?php

namespace App\Filament\Resources\EducationLevels\Pages;

use App\Filament\Resources\EducationLevels\EducationLevelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditEducationLevel extends EditRecord
{
    protected static string $resource = EducationLevelResource::class;

    protected Width | string | null $maxWidth = Width::Full;

    public function getTitle(): string
    {
        return 'Chỉnh sửa Cấp học / Danh mục';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Xóa Cấp học'),
        ];
    }
}
