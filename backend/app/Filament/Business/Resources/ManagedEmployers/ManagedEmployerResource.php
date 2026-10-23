<?php

namespace App\Filament\Business\Resources\ManagedEmployers;

use App\Filament\Business\Resources\ManagedEmployers\Pages\EditManagedEmployer;
use App\Filament\Business\Resources\ManagedEmployers\Pages\ListManagedEmployers;
use App\Models\Employer;
use App\Models\Ward;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ManagedEmployerResource extends Resource
{
    protected static ?string $model = Employer::class;
    protected static ?string $modelLabel = 'Thông tin doanh nghiệp';
    protected static ?string $pluralModelLabel = 'Thông tin doanh nghiệp';
    protected static ?string $navigationLabel = 'Thông tin doanh nghiệp';
    protected static \UnitEnum|string|null $navigationGroup = 'Quản lý doanh nghiệp';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool { return Filament::auth()->user()?->isBusinessAdmin() ?? false; }
    public static function canCreate(): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }
    public static function canEdit(Model $record): bool { return Filament::auth()->user()?->canManageEmployer($record) ?? false; }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereKey(Filament::auth()->user()?->employer_id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Hồ sơ doanh nghiệp')->description('Cập nhật thông tin liên hệ và địa chỉ của doanh nghiệp.')->columns(2)->schema([
                TextInput::make('name')->label('Tên doanh nghiệp')->required()->maxLength(200)->columnSpanFull(),
                TextInput::make('tax_code')->label('Mã số thuế')->disabled(),
                TextInput::make('industry')->label('Lĩnh vực hoạt động')->maxLength(150),
                TextInput::make('region')->label('Khu vực')->maxLength(150),
                TextInput::make('employee_count')->label('Quy mô lao động')->numeric()->minValue(0),
                Select::make('ward_id')->label('Phường / Xã')->searchable()->options(fn (): array => Ward::query()->orderBy('name')->pluck('full_name', 'id')->all()),
                TextInput::make('address')->label('Địa chỉ')->maxLength(255)->columnSpanFull(),
                TextInput::make('contact_person')->label('Người liên hệ')->maxLength(120),
                TextInput::make('phone')->label('Số điện thoại')->tel()->maxLength(50),
                TextInput::make('email')->label('Email')->email()->maxLength(120),
                TextInput::make('website')->label('Website')->url()->maxLength(255),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Doanh nghiệp')->weight('bold')->wrap(),
            TextColumn::make('tax_code')->label('Mã số thuế')->placeholder('Chưa cập nhật'),
            TextColumn::make('industry')->label('Lĩnh vực')->placeholder('Chưa cập nhật'),
            TextColumn::make('employee_count')->label('Quy mô lao động')->numeric()->placeholder('—')->alignEnd(),
            IconColumn::make('is_verified')->label('Đã xác minh')->boolean(),
        ])->recordActions([EditAction::make()->label('Cập nhật thông tin')]);
    }

    public static function getPages(): array
    {
        return ['index' => ListManagedEmployers::route('/'), 'edit' => EditManagedEmployer::route('/{record}/edit')];
    }
}
