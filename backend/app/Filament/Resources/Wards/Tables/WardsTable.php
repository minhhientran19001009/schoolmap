<?php

namespace App\Filament\Resources\Wards\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WardsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Tên Phường / Xã')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('unit_type')
                    ->label('Phân loại')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Phường' => 'indigo',
                        'Xã' => 'success',
                        'Thị trấn' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('postal_code')
                    ->label('Mã Bưu chính')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono'),
            ])
            ->filters([
                SelectFilter::make('unit_type')
                    ->label('Lọc theo Phân loại')
                    ->options([
                        'Phường' => 'Phường (32 đơn vị)',
                        'Xã' => 'Xã (97 đơn vị)',
                        'Thị trấn' => 'Thị trấn',
                    ]),
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
            ->defaultSort('full_name', 'asc')
            ->defaultPaginationPageOption(25);
    }
}
