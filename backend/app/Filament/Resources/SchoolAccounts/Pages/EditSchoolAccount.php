<?php

namespace App\Filament\Resources\SchoolAccounts\Pages;

use App\Filament\Resources\SchoolAccounts\SchoolAccountResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditSchoolAccount extends EditRecord
{
    protected static string $resource = SchoolAccountResource::class;

    protected Width | string | null $maxWidth = Width::Full;

    public function getTitle(): string
    {
        return 'Chỉnh sửa tài khoản nhà trường';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Xóa tài khoản'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['role'] = 'SCHOOL_ADMIN';
        $data['name'] = $data['full_name'] ?? $data['username'] ?? 'Tài khoản nhà trường';
        $data['username'] = $data['email'] ?? $data['username'] ?? null;

        return $data;
    }
}
