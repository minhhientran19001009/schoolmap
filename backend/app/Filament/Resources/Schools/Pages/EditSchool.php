<?php

namespace App\Filament\Resources\Schools\Pages;

use App\Filament\Resources\Schools\SchoolResource;
use App\Models\EducationLevel;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditSchool extends EditRecord
{
    protected static string $resource = SchoolResource::class;

    protected Width | string | null $maxWidth = Width::Full;

    public function getTitle(): string
    {
        return 'Chỉnh sửa Cơ sở Giáo dục';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Xóa Trường học'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['educationLevels']) && is_array($data['educationLevels'])) {
            $first = reset($data['educationLevels']);
            if (is_numeric($first) || is_string($first)) {
                $data['education_level_id'] = (string) $first;
            }
        }

        if (!empty($data['education_level_id']) && !EducationLevel::where('id', $data['education_level_id'])->exists()) {
            $data['education_level_id'] = EducationLevel::first()?->id ?? 'cao_dang';
        }

        return $data;
    }
}
