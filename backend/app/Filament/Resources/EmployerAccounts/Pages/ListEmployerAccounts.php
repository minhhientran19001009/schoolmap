<?php

namespace App\Filament\Resources\EmployerAccounts\Pages;
use App\Filament\Resources\EmployerAccounts\EmployerAccountResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListEmployerAccounts extends ListRecords { protected static string $resource = EmployerAccountResource::class; protected function getHeaderActions(): array { return [CreateAction::make()->label('Thêm tài khoản doanh nghiệp')]; } }
