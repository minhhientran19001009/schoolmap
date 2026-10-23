<?php

namespace App\Filament\School\Resources\TrainingCapacities\Pages;
use App\Filament\School\Resources\TrainingCapacities\TrainingCapacityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
class EditTrainingCapacity extends EditRecord
{
    protected static string $resource = TrainingCapacityResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! in_array((string) ($data['school_id'] ?? ''), TrainingCapacityResource::allowedSchoolIds(), true)) {
            throw ValidationException::withMessages(['school_id' => 'Bạn không có quyền khai báo cho cơ sở này.']);
        }
        return $data;
    }
}
