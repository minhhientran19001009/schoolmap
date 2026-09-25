<?php

namespace App\Filament\School\Resources\ManagedSchools\Pages;

use App\Filament\School\Resources\ManagedSchools\ManagedSchoolResource;
use App\Models\EducationLevel;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateManagedSchool extends CreateRecord
{
    protected static string $resource = ManagedSchoolResource::class;

    public function getTitle(): string
    {
        return 'Thêm cơ sở trực thuộc';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Filament::auth()->user();
        $data['campus_type'] = 'CAMPUS';
        $data['parent_school_id'] = $user?->school_id;

        if (! empty($data['educationLevels']) && is_array($data['educationLevels'])) {
            $data['education_level_id'] = (string) reset($data['educationLevels']);
        }

        if (empty($data['education_level_id']) || ! EducationLevel::where('id', $data['education_level_id'])->exists()) {
            $data['education_level_id'] = EducationLevel::first()?->id ?? 'cao_dang';
        }

        return $data;
    }
}
