<?php

namespace App\Filament\Resources\Schools\Tables;

use App\Models\School;
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

                TextColumn::make('campus_type')
                    ->label('Loại cơ sở')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => School::campusTypeLabels()[$state ?? 'MAIN'] ?? 'Không xác định')
                    ->color(fn (?string $state): string => $state === 'MAIN' ? 'success' : 'info')
                    ->sortable(),

                TextColumn::make('parentCampus.name')
                    ->label('Cơ sở chính')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('educationLevels.name')
                    ->label('Cấp học')
                    ->badge()
                    ->color('primary')
                    ->separator(', '),

                TextColumn::make('ward')
                    ->label('Phường / Xã')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('website')
                    ->label('Website')
                    ->icon('heroicon-m-globe-alt')
                    ->url(fn ($record) => $record->website)
                    ->openUrlInNewTab()
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('educationLevels')
                    ->label('Lọc theo Cấp học / Danh mục')
                    ->relationship('educationLevels', 'name'),

                SelectFilter::make('campus_type')
                    ->label('Lọc theo loại cơ sở')
                    ->options(School::campusTypeLabels()),
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
