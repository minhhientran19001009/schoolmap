<?php

namespace App\Filament\Business\Resources\ManagedWorkforceDemands\Pages;
use App\Filament\Business\Resources\ManagedWorkforceDemands\ManagedWorkforceDemandResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
class CreateManagedWorkforceDemand extends CreateRecord
{
    protected static string $resource = ManagedWorkforceDemandResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['employer_id'] = Filament::auth()->user()->employer_id;
        $data['source'] = 'SYSTEM';
        $data['is_sample'] = false;
        return $data;
    }
}
