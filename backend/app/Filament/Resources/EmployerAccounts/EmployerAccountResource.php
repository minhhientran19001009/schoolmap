<?php

namespace App\Filament\Resources\EmployerAccounts;

use App\Filament\Resources\EmployerAccounts\Pages\CreateEmployerAccount;
use App\Filament\Resources\EmployerAccounts\Pages\EditEmployerAccount;
use App\Filament\Resources\EmployerAccounts\Pages\ListEmployerAccounts;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EmployerAccountResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $modelLabel = 'Tài khoản doanh nghiệp';
    protected static ?string $pluralModelLabel = 'Tài khoản doanh nghiệp';
    protected static ?string $navigationLabel = 'Tài khoản doanh nghiệp';
    protected static \UnitEnum|string|null $navigationGroup = 'Quản lý tài khoản';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool { return Filament::auth()->user()?->isSystemAdmin() ?? false; }
    public static function canEdit(Model $record): bool { return static::canAccess() && $record instanceof User && $record->role === 'BUSINESS_ADMIN'; }
    public static function canDelete(Model $record): bool { return static::canEdit($record); }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'BUSINESS_ADMIN')->with('employer');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thông tin tài khoản')->columns(2)->schema([
                Select::make('employer_id')->label('Doanh nghiệp')->relationship('employer', 'name')->searchable()->preload()->required()->unique(ignoreRecord: true),
                TextInput::make('full_name')->label('Người phụ trách')->required()->maxLength(100),
                TextInput::make('email')->label('Email đăng nhập')->email()->required()->unique(ignoreRecord: true)->maxLength(100),
                TextInput::make('password')->label('Mật khẩu')->password()->revealable()->minLength(8)->required(fn (?User $record): bool => $record === null)->dehydrated(fn (?string $state): bool => filled($state))->formatStateUsing(fn (): ?string => null),
                Toggle::make('is_active')->label('Tài khoản đang hoạt động')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('employer.name')->label('Doanh nghiệp')->searchable()->sortable()->weight('bold')->wrap(),
            TextColumn::make('full_name')->label('Người phụ trách')->searchable(),
            TextColumn::make('email')->label('Email')->searchable(),
            TextColumn::make('last_login_at')->label('Đăng nhập gần nhất')->dateTime('d/m/Y H:i')->placeholder('Chưa đăng nhập'),
            IconColumn::make('is_active')->label('Hoạt động')->boolean(),
        ])->recordActions([EditAction::make(), DeleteAction::make()])->defaultSort('employer.name');
    }

    public static function getPages(): array
    {
        return ['index' => ListEmployerAccounts::route('/'), 'create' => CreateEmployerAccount::route('/create'), 'edit' => EditEmployerAccount::route('/{record}/edit')];
    }
}
