<?php

namespace App\Filament\Resources\SchoolAccounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SchoolAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 1, 'xl' => 2])
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Trường được quản lý')
                            ->description('Mỗi trường chỉ được cấp một tài khoản quản trị nhà trường.')
                            ->schema([
                                Select::make('school_id')
                                    ->label('Trường chính')
                                    ->relationship(
                                        'school',
                                        'name',
                                        modifyQueryUsing: fn ($query) => $query
                                            ->where('campus_type', 'MAIN')
                                            ->orderBy('name'),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Tài khoản được quản lý trường này và các cơ sở trực thuộc.'),

                                TextInput::make('full_name')
                                    ->label('Người phụ trách')
                                    ->required()
                                    ->maxLength(100),
                            ]),

                        Section::make('Thông tin đăng nhập')
                            ->description('Nhà trường sẽ đăng nhập bằng email và mật khẩu được cấp.')
                            ->schema([
                                TextInput::make('email')
                                    ->label('Email đăng nhập')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(100)
                                    ->autocomplete('email'),

                                TextInput::make('password')
                                    ->label('Mật khẩu')
                                    ->password()
                                    ->revealable()
                                    ->minLength(8)
                                    ->required(fn (?\App\Models\User $record): bool => $record === null)
                                    ->dehydrated(fn (?string $state): bool => filled($state))
                                    ->formatStateUsing(fn (): ?string => null)
                                    ->autocomplete('new-password')
                                    ->helperText('Để trống khi chỉnh sửa nếu không muốn đổi mật khẩu.'),

                                Toggle::make('is_active')
                                    ->label('Tài khoản đang hoạt động')
                                    ->default(true)
                                    ->inline(false),
                            ]),
                    ]),
            ]);
    }
}
