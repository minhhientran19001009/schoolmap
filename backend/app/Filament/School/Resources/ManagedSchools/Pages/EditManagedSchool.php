<?php

namespace App\Filament\School\Resources\ManagedSchools\Pages;

use App\Filament\School\Resources\ManagedSchools\ManagedSchoolResource;
use App\Models\EducationLevel;
use Filament\Resources\Pages\EditRecord;

class EditManagedSchool extends EditRecord
{
    protected static string $resource = ManagedSchoolResource::class;

    public function getTitle(): string
    {
        return $this->record->campus_type === 'MAIN'
            ? 'Chỉnh sửa thông tin trường'
            : 'Chỉnh sửa cơ sở trực thuộc';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['campus_type'] = $this->record->campus_type;
        $data['parent_school_id'] = $this->record->parent_school_id;

        if (! empty($data['educationLevels']) && is_array($data['educationLevels'])) {
            $data['education_level_id'] = (string) reset($data['educationLevels']);
        }

        if (! empty($data['education_level_id']) && ! EducationLevel::where('id', $data['education_level_id'])->exists()) {
            $data['education_level_id'] = EducationLevel::first()?->id ?? 'cao_dang';
        }

        return $data;
    }
}
