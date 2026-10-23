<?php

namespace App\Filament\Resources\TrainingCapacities\Pages;
use App\Filament\Resources\TrainingCapacities\TrainingCapacityResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
class EditTrainingCapacity extends EditRecord { protected static string $resource = TrainingCapacityResource::class; protected function getHeaderActions(): array { return [DeleteAction::make()]; } }
