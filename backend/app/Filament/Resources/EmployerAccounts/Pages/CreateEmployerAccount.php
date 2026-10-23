<?php

namespace App\Filament\Resources\EmployerAccounts\Pages;
use App\Filament\Resources\EmployerAccounts\EmployerAccountResource;
use Filament\Resources\Pages\CreateRecord;
class CreateEmployerAccount extends CreateRecord
{
    protected static string $resource = EmployerAccountResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = 'BUSINESS_ADMIN';
        $data['name'] = $data['full_name'] ?? 'Tài khoản doanh nghiệp';
        $data['username'] = $data['email'] ?? null;
        $data['must_change_password'] = false;
        return $data;
    }
}
