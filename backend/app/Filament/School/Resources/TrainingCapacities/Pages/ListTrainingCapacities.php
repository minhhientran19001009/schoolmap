<?php

namespace App\Filament\School\Resources\TrainingCapacities\Pages;
use App\Filament\School\Resources\TrainingCapacities\TrainingCapacityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListTrainingCapacities extends ListRecords { protected static string $resource = TrainingCapacityResource::class; protected function getHeaderActions(): array { return [CreateAction::make()->label('Khai báo năng lực')]; } }
