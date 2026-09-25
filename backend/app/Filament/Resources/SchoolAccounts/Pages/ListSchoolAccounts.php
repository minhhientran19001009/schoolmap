<?php

namespace App\Filament\Resources\SchoolAccounts\Pages;

use App\Filament\Resources\SchoolAccounts\SchoolAccountResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSchoolAccounts extends ListRecords
{
    protected static string $resource = SchoolAccountResource::class;

    public function getTitle(): string
    {
        return 'Tài khoản nhà trường';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Thêm tài khoản nhà trường'),
        ];
    }
}
