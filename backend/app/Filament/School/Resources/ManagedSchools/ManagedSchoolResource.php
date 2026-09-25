<?php

namespace App\Filament\School\Resources\ManagedSchools;

use App\Filament\School\Resources\ManagedSchools\Pages\CreateManagedSchool;
use App\Filament\School\Resources\ManagedSchools\Pages\EditManagedSchool;
use App\Filament\School\Resources\ManagedSchools\Pages\ListManagedSchools;
use App\Filament\School\Resources\ManagedSchools\Schemas\ManagedSchoolForm;
use App\Models\School;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ManagedSchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static ?string $modelLabel = 'Trường học / Cơ sở';
    protected static ?string $pluralModelLabel = 'Thông tin trường và cơ sở';
    protected static ?string $navigationLabel = 'Thông tin trường và cơ sở';
    protected static \UnitEnum|string|null $navigationGroup = 'Quản lý trường học';
    protected static ?int $navigationSort = 1;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    public static function canAccess(): bool
    {
        return Filament::auth()->user()?->isSchoolAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return static::canAccess();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canAccess()
            && $record instanceof School
            && (Filament::auth()->user()?->canManageSchool($record) ?? false);
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        if (! $user?->isSchoolAdmin() || blank($user->school_id)) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return parent::getEloquentQuery()
            ->where(function (Builder $query) use ($user): void {
                $query
                    ->whereKey($user->school_id)
                    ->orWhere('parent_school_id', $user->school_id);
            })
            ->with(['parentCampus', 'educationLevels']);
    }

    public static function form(Schema $schema): Schema
    {
        return ManagedSchoolForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label('Tên trường / cơ sở')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                \Filament\Tables\Columns\TextColumn::make('campus_type')
                    ->label('Loại cơ sở')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => School::campusTypeLabels()[$state ?? 'MAIN'] ?? 'Cơ sở giáo dục')
                    ->color(fn (?string $state): string => $state === 'MAIN' ? 'success' : 'info'),

                \Filament\Tables\Columns\TextColumn::make('ward')
                    ->label('Phường / Xã')
                    ->searchable()
                    ->wrap(),

                \Filament\Tables\Columns\TextColumn::make('phone')
                    ->label('Số điện thoại')
                    ->placeholder('Chưa cập nhật'),

                \Filament\Tables\Columns\TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->placeholder('Chưa xác định'),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make()->label('Chỉnh sửa'),
            ])
            ->defaultSort('name', 'asc')
            ->defaultPaginationPageOption(25);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListManagedSchools::route('/'),
            'create' => CreateManagedSchool::route('/create'),
            'edit' => EditManagedSchool::route('/{record}/edit'),
        ];
    }
}
