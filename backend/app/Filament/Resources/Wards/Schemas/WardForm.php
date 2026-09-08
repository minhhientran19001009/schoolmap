<?php

namespace App\Filament\Resources\Wards\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin Phường / Xã')
                    ->description('Quản lý các trường thông tin chính yếu của đơn vị hành chính')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('full_name')
                                ->label('Tên Phường / Xã')
                                ->placeholder('VD: Phường Châu Sơn, Xã Cúc Phương,...')
                                ->required(),

                            Select::make('unit_type')
                                ->label('Phân loại đơn vị')
                                ->options([
                                    'Phường' => 'Phường',
                                    'Xã' => 'Xã',
                                    'Thị trấn' => 'Thị trấn',
                                ])
                                ->required(),

                            TextInput::make('postal_code')
                                ->label('Mã Bưu chính')
                                ->placeholder('VD: 18115')
                                ->required(),
                        ]),
                    ]),
            ]);
    }
}
