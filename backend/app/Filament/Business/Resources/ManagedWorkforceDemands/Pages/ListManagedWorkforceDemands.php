<?php

namespace App\Filament\Business\Resources\ManagedWorkforceDemands\Pages;
use App\Filament\Business\Resources\ManagedWorkforceDemands\ManagedWorkforceDemandResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListManagedWorkforceDemands extends ListRecords { protected static string $resource = ManagedWorkforceDemandResource::class; protected function getHeaderActions(): array { return [CreateAction::make()->label('Đăng nhu cầu')]; } }
