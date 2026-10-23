<?php

namespace App\Filament\Resources\WorkforceDemands\Pages;
use App\Filament\Resources\WorkforceDemands\WorkforceDemandResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListWorkforceDemands extends ListRecords { protected static string $resource = WorkforceDemandResource::class; protected function getHeaderActions(): array { return [CreateAction::make()->label('Thêm nhu cầu')]; } }
