<?php

namespace App\Filament\Resources\TrainingCapacities\Pages;
use App\Filament\Resources\TrainingCapacities\TrainingCapacityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListTrainingCapacities extends ListRecords { protected static string $resource = TrainingCapacityResource::class; protected function getHeaderActions(): array { return [CreateAction::make()->label('Thêm năng lực')]; } }
