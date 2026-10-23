<?php

namespace App\Filament\School\Resources\TrainingCapacities\Pages;
use App\Filament\School\Resources\TrainingCapacities\TrainingCapacityResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
class CreateTrainingCapacity extends CreateRecord
{
    protected static string $resource = TrainingCapacityResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! in_array((string) ($data['school_id'] ?? ''), TrainingCapacityResource::allowedSchoolIds(), true)) {
            throw ValidationException::withMessages(['school_id' => 'Bạn không có quyền khai báo cho cơ sở này.']);
        }
        $data['source'] = 'SYSTEM';
        $data['is_sample'] = false;
        return $data;
    }
}
