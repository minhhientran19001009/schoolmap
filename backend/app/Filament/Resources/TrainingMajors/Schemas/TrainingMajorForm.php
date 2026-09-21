<?php

namespace App\Filament\Resources\TrainingMajors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TrainingMajorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin chuyên ngành đào tạo')
                    ->description('Danh mục dùng chung để gán cho nhiều trường trên bản đồ.')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên chuyên ngành / nghề đào tạo')
                            ->placeholder('VD: Công nghệ kỹ thuật ô tô')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Toggle::make('is_active')
                            ->label('Đang sử dụng')
                            ->default(true)
                            ->helperText('Ngành không còn tuyển sinh có thể tắt để không xuất hiện trong bộ lọc mới.'),
                    ]),
            ]);
    }
}
