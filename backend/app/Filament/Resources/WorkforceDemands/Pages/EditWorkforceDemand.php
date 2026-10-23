<?php

namespace App\Filament\Resources\WorkforceDemands\Pages;
use App\Filament\Resources\WorkforceDemands\WorkforceDemandResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
class EditWorkforceDemand extends EditRecord { protected static string $resource = WorkforceDemandResource::class; protected function getHeaderActions(): array { return [DeleteAction::make()]; } }
