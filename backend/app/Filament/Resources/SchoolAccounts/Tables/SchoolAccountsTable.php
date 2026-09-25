<?php

namespace App\Filament\Resources\SchoolAccounts\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchoolAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('school.name')
                    ->label('Trường học')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('full_name')
                    ->label('Người phụ trách')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email đăng nhập')
                    ->searchable(),

                TextColumn::make('last_login_at')
                    ->label('Đăng nhập gần nhất')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Chưa đăng nhập')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Hoạt động')
                    ->boolean()
                    ->sortable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->label('Chỉnh sửa'),
                    Action::make('toggleActive')
                        ->label(fn (User $record): string => $record->is_active ? 'Khóa tài khoản' : 'Mở khóa tài khoản')
                        ->icon(fn (User $record): string => $record->is_active ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                        ->color(fn (User $record): string => $record->is_active ? 'danger' : 'success')
                        ->requiresConfirmation()
                        ->action(function (User $record): void {
                            $record->update([
                                'is_active' => ! $record->is_active,
                                'locked_at' => $record->is_active ? now() : null,
                            ]);
                        }),
                    DeleteAction::make()
                        ->label('Xóa tài khoản')
                        ->before(function (User $record): void {
                            $record->update([
                                'is_active' => false,
                                'locked_at' => now(),
                            ]);
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xóa tài khoản đã chọn'),
                ]),
            ])
            ->defaultSort('school.name', 'asc')
            ->defaultPaginationPageOption(25);
    }
}
