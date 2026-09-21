<?php

namespace App\Filament\Resources\TrainingMajors\Pages;

use App\Filament\Resources\TrainingMajors\TrainingMajorResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditTrainingMajor extends EditRecord
{
    protected static string $resource = TrainingMajorResource::class;

    protected Width|string|null $maxWidth = Width::Full;

    public function getTitle(): string
    {
        return 'Chỉnh sửa chuyên ngành đào tạo';
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Xóa chuyên ngành'),
        ];
    }
}
