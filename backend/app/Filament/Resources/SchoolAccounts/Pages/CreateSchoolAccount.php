<?php

namespace App\Filament\Resources\SchoolAccounts\Pages;

use App\Filament\Resources\SchoolAccounts\SchoolAccountResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateSchoolAccount extends CreateRecord
{
    protected static string $resource = SchoolAccountResource::class;

    protected Width | string | null $maxWidth = Width::Full;

    public function getTitle(): string
    {
        return 'Thêm tài khoản nhà trường';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = 'SCHOOL_ADMIN';
        $data['name'] = $data['full_name'] ?? $data['username'] ?? 'Tài khoản nhà trường';
        $data['username'] = $data['email'] ?? $data['username'] ?? null;
        $data['must_change_password'] = false;

        return $data;
    }
}
