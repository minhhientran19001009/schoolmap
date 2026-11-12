<?php

namespace App\Filament\Resources\CareerFields\Pages;

use App\Filament\Resources\CareerFields\CareerFieldResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCareerFields extends ListRecords
{
    protected static string $resource = CareerFieldResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()->label('Thêm lĩnh vực')];
    }
}
