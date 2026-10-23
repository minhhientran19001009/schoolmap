<?php

namespace App\Filament\Resources\ReportingPeriods;

use App\Filament\Resources\ReportingPeriods\Pages\CreateReportingPeriod;
use App\Filament\Resources\ReportingPeriods\Pages\EditReportingPeriod;
use App\Filament\Resources\ReportingPeriods\Pages\ListReportingPeriods;
use App\Models\ReportingPeriod;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReportingPeriodResource extends Resource
{
    protected static ?string $model = ReportingPeriod::class;
    protected static ?string $modelLabel = 'Kỳ báo cáo';
    protected static ?string $pluralModelLabel = 'Kỳ báo cáo';
    protected static ?string $navigationLabel = 'Kỳ báo cáo';
    protected static \UnitEnum|string|null $navigationGroup = 'Kết nối cung - cầu';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return Filament::auth()->user()?->isSystemAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thông tin kỳ báo cáo')->columns(2)->schema([
                TextInput::make('code')->label('Mã kỳ')->required()->unique(ignoreRecord: true)->maxLength(30),
                TextInput::make('name')->label('Tên kỳ')->required()->maxLength(100),
                DatePicker::make('starts_at')->label('Ngày bắt đầu')->required(),
                DatePicker::make('ends_at')->label('Ngày kết thúc')->required()->afterOrEqual('starts_at'),
                Toggle::make('is_active')->label('Đang sử dụng')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->label('Mã kỳ')->searchable()->sortable()->badge(),
            TextColumn::make('name')->label('Tên kỳ')->searchable()->sortable()->weight('bold'),
            TextColumn::make('starts_at')->label('Bắt đầu')->date('d/m/Y')->sortable(),
            TextColumn::make('ends_at')->label('Kết thúc')->date('d/m/Y')->sortable(),
            IconColumn::make('is_active')->label('Đang dùng')->boolean(),
        ])->recordActions([
            EditAction::make()->label('Chỉnh sửa'),
            DeleteAction::make()->label('Xóa'),
        ])->defaultSort('starts_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReportingPeriods::route('/'),
            'create' => CreateReportingPeriod::route('/create'),
            'edit' => EditReportingPeriod::route('/{record}/edit'),
        ];
    }
}
