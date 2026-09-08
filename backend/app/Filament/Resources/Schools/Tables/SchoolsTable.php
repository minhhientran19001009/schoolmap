<?php

namespace App\Filament\Resources\Schools\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SchoolsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên Cơ sở Giáo dục')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('educationLevels.name')
                    ->label('Cấp học')
                    ->badge()
                    ->color('primary')
                    ->separator(', '),

                TextColumn::make('ward')
                    ->label('Phường / Xã')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('educationLevels')
                    ->label('Lọc theo Cấp học / Danh mục')
                    ->relationship('educationLevels', 'name'),
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
                    DeleteBulkAction::make()->label('Xóa mục đã chọn'),
                ]),
            ])
            ->defaultSort('name', 'asc')
            ->defaultPaginationPageOption(25);
    }
}
