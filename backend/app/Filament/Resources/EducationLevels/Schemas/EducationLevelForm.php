<?php

namespace App\Filament\Resources\EducationLevels\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EducationLevelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin Cấp học / Danh mục')
                    ->description('Cấu hình các danh mục phân loại cơ sở giáo dục')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Tên Cấp học / Danh mục')
                                ->placeholder('VD: Mầm non, Tiểu học, Cao đẳng,...')
                                ->required(),

                            ColorPicker::make('color')
                                ->label('Màu sắc nhận diện')
                                ->default('#3b82f6')
                                ->required(),
                        ]),
                    ]),
            ]);
    }
}
