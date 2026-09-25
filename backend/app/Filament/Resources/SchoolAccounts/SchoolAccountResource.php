<?php

namespace App\Filament\Resources\SchoolAccounts;

use App\Filament\Resources\SchoolAccounts\Pages\CreateSchoolAccount;
use App\Filament\Resources\SchoolAccounts\Pages\EditSchoolAccount;
use App\Filament\Resources\SchoolAccounts\Pages\ListSchoolAccounts;
use App\Filament\Resources\SchoolAccounts\Schemas\SchoolAccountForm;
use App\Filament\Resources\SchoolAccounts\Tables\SchoolAccountsTable;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SchoolAccountResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Tài khoản nhà trường';
    protected static ?string $pluralModelLabel = 'Tài khoản nhà trường';
    protected static ?string $navigationLabel = 'Tài khoản nhà trường';
    protected static \UnitEnum|string|null $navigationGroup = 'Quản lý tài khoản';
    protected static ?int $navigationSort = 1;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    public static function canAccess(): bool
    {
        return Filament::auth()->user()?->isSystemAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return static::canAccess();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canAccess() && $record instanceof User && $record->role === 'SCHOOL_ADMIN';
    }

    public static function canDelete(Model $record): bool
    {
        return static::canAccess() && $record instanceof User && $record->role === 'SCHOOL_ADMIN';
    }

    public static function canDeleteAny(): bool
    {
        return static::canAccess();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', 'SCHOOL_ADMIN')
            ->with('school');
    }

    public static function form(Schema $schema): Schema
    {
        return SchoolAccountForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SchoolAccountsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSchoolAccounts::route('/'),
            'create' => CreateSchoolAccount::route('/create'),
            'edit' => EditSchoolAccount::route('/{record}/edit'),
        ];
    }
}
