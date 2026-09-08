<?php

namespace App\Filament\Resources\Wards\Pages;

use App\Filament\Resources\Wards\WardResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditWard extends EditRecord
{
    protected static string $resource = WardResource::class;

    protected Width | string | null $maxWidth = Width::Full;

    public function getTitle(): string
    {
        return 'Chỉnh sửa Phường / Xã';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Xóa Phường / Xã'),
        ];
    }
}
