<?php

namespace App\Filament\Resources\CareerFields\Pages;

use App\Filament\Resources\CareerFields\CareerFieldResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCareerField extends EditRecord
{
    protected static string $resource = CareerFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()->label('Xóa')];
    }
}
