<?php

namespace App\Filament\Resources\EmployerAccounts\Pages;
use App\Filament\Resources\EmployerAccounts\EmployerAccountResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
class EditEmployerAccount extends EditRecord
{
    protected static string $resource = EmployerAccountResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['role'] = 'BUSINESS_ADMIN';
        $data['name'] = $data['full_name'] ?? 'Tài khoản doanh nghiệp';
        $data['username'] = $data['email'] ?? null;
        return $data;
    }
}
