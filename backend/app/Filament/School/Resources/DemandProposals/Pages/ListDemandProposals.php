<?php

namespace App\Filament\School\Resources\DemandProposals\Pages;
use App\Filament\School\Resources\DemandProposals\DemandProposalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListDemandProposals extends ListRecords { protected static string $resource = DemandProposalResource::class; protected function getHeaderActions(): array { return [CreateAction::make()->label('Tạo đề xuất đáp ứng')]; } }
