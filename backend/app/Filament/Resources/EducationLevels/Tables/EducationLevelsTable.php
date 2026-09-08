<?php

namespace App\Filament\Resources\EducationLevels\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EducationLevelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên Cấp học / Danh mục')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('schools_count')
                    ->label('Số lượng trường')
                    ->counts('schools')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->alignEnd(),

                ColorColumn::make('color')
                    ->label('Màu sắc'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->label('Chi tiết'),
                    EditAction::make()->label('Chỉnh sửa'),
                    DeleteAction::make()->label('Xóa'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xóa các mục đã chọn'),
                ]),
            ])
            ->defaultSort('name', 'asc');
    }
}
