<?php

namespace App\Filament\Business\Resources\ManagedWorkforceDemands\Pages;
use App\Filament\Business\Resources\ManagedWorkforceDemands\ManagedWorkforceDemandResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
class EditManagedWorkforceDemand extends EditRecord { protected static string $resource = ManagedWorkforceDemandResource::class; protected function getHeaderActions(): array { return [DeleteAction::make()]; } }
