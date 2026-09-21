<?php

namespace App\Filament\Resources\TrainingMajors\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TrainingMajorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên chuyên ngành / nghề')
                    // The default MySQL collation is accent-insensitive, so
                    // searching "hàn" incorrectly also matched "Chăn".
                    // Keep Vietnamese diacritics significant while retaining
                    // case-insensitive matching.
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $search = mb_strtolower(trim($search), 'UTF-8');

                        return $query->whereRaw(
                            'LOWER(`name`) COLLATE utf8mb4_bin LIKE ?',
                            ["%{$search}%"],
                        );
                    })
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('schools_count')
                    ->label('Số trường')
                    ->counts('schools')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->alignEnd(),

                IconColumn::make('is_active')
                    ->label('Đang dùng')
                    ->boolean()
                    ->sortable(),
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
