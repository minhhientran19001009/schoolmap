<?php

namespace App\Filament\Business\Resources\ManagedWorkforceDemands;

use App\Filament\Business\Resources\ManagedWorkforceDemands\Pages\CreateManagedWorkforceDemand;
use App\Filament\Business\Resources\ManagedWorkforceDemands\Pages\EditManagedWorkforceDemand;
use App\Filament\Business\Resources\ManagedWorkforceDemands\Pages\ListManagedWorkforceDemands;
use App\Models\ReportingPeriod;
use App\Models\TrainingLevel;
use App\Models\WorkforceDemand;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ManagedWorkforceDemandResource extends Resource
{
    protected static ?string $model = WorkforceDemand::class;
    protected static ?string $modelLabel = 'Nhu cầu nhân lực';
    protected static ?string $pluralModelLabel = 'Nhu cầu nhân lực';
    protected static ?string $navigationLabel = 'Nhu cầu nhân lực';
    protected static \UnitEnum|string|null $navigationGroup = 'Kết nối cung - cầu';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool { return Filament::auth()->user()?->isBusinessAdmin() ?? false; }
    public static function canEdit(Model $record): bool { return static::canAccess() && $record instanceof WorkforceDemand && (int) $record->employer_id === (int) Filament::auth()->user()?->employer_id; }
    public static function canDelete(Model $record): bool { return static::canEdit($record); }
    public static function getEloquentQuery(): Builder { return parent::getEloquentQuery()->where('employer_id', Filament::auth()->user()?->employer_id); }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Khai báo nhu cầu nhân lực')->description('Khai báo nhu cầu theo ngành, trình độ và từng kỳ báo cáo.')->columns(2)->schema([
                Select::make('reporting_period_id')->label('Kỳ báo cáo')->required()->searchable()->options(fn (): array => ReportingPeriod::query()->where('is_active', true)->orderByDesc('starts_at')->pluck('name', 'id')->all()),
                Select::make('training_major_id')->label('Ngành / nghề')->relationship('major', 'name', modifyQueryUsing: fn ($query) => $query->where('is_active', true)->orderBy('name'))->searchable()->preload()->required(),
                Select::make('education_level_id')->label('Trình độ')->required()->options(fn (): array => TrainingLevel::query()->where('is_active', true)->orderBy('display_order')->pluck('name', 'id')->all()),
                Select::make('purpose')->label('Mục đích')->required()->default('RECRUITMENT')->options(['RECRUITMENT' => 'Tuyển dụng', 'NEW_TRAINING' => 'Đào tạo mới', 'RETRAINING' => 'Đào tạo lại']),
                TextInput::make('quantity')->label('Số người cần')->numeric()->minValue(1)->maxValue(100000)->required(),
                TextInput::make('location')->label('Khu vực / địa điểm')->maxLength(200),
                Select::make('skills')->label('Kỹ năng yêu cầu')->relationship('skills', 'name')->multiple()->searchable()->preload()->columnSpanFull(),
                TextInput::make('age_range')->label('Độ tuổi')->maxLength(80),
                TextInput::make('salary')->label('Thu nhập dự kiến')->maxLength(100),
                Textarea::make('note')->label('Ghi chú / điều kiện phối hợp')->rows(4)->maxLength(2000)->columnSpanFull(),
                Select::make('status')->label('Trạng thái')->required()->default('DRAFT')->options(['DRAFT' => 'Bản nháp', 'PUBLISHED' => 'Công bố nhu cầu', 'CLOSED' => 'Đã đóng']),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('major.name')->label('Ngành / nghề')->searchable()->weight('bold')->wrap(),
            TextColumn::make('trainingLevel.name')->label('Trình độ')->badge(),
            TextColumn::make('period.name')->label('Kỳ'),
            TextColumn::make('quantity')->label('Số người')->numeric()->alignEnd(),
            TextColumn::make('proposals_sum_quantity')->label('Đã đề xuất')->sum('proposals', 'quantity')->numeric()->default(0)->alignEnd(),
            TextColumn::make('status')->label('Trạng thái')->badge()->formatStateUsing(fn (string $state): string => match ($state) {'PUBLISHED' => 'Đã công bố', 'CLOSED' => 'Đã đóng', default => 'Bản nháp'})->color(fn (string $state): string => match ($state) {'PUBLISHED' => 'success', 'CLOSED' => 'gray', default => 'warning'}),
        ])->recordActions([EditAction::make(), DeleteAction::make()])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListManagedWorkforceDemands::route('/'), 'create' => CreateManagedWorkforceDemand::route('/create'), 'edit' => EditManagedWorkforceDemand::route('/{record}/edit')];
    }
}
