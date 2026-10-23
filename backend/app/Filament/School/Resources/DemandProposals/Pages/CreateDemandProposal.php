<?php

namespace App\Filament\School\Resources\DemandProposals\Pages;
use App\Filament\School\Resources\DemandProposals\DemandProposalResource;
use App\Filament\School\Resources\TrainingCapacities\TrainingCapacityResource;
use App\Models\TrainingCapacity;
use App\Models\WorkforceDemand;
use App\Services\SupplyDemandMatchingService;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
class CreateDemandProposal extends CreateRecord
{
    protected static string $resource = DemandProposalResource::class;
    protected function handleRecordCreation(array $data): Model
    {
        $demand = WorkforceDemand::query()->findOrFail($data['workforce_demand_id']);
        $capacity = TrainingCapacity::query()->findOrFail($data['training_capacity_id']);
        if (! in_array((string) $capacity->school_id, TrainingCapacityResource::allowedSchoolIds(), true)) {
            throw ValidationException::withMessages(['training_capacity_id' => 'Bạn không có quyền sử dụng năng lực này.']);
        }
        return app(SupplyDemandMatchingService::class)->createProposal($demand, $capacity, (int) $data['quantity'], (string) $data['plan'], Filament::auth()->id());
    }
}
