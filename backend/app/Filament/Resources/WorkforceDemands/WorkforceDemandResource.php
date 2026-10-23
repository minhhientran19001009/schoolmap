<?php

namespace App\Filament\Resources\WorkforceDemands;

use App\Filament\Resources\WorkforceDemands\Pages\CreateWorkforceDemand;
use App\Filament\Resources\WorkforceDemands\Pages\EditWorkforceDemand;
use App\Filament\Resources\WorkforceDemands\Pages\ListWorkforceDemands;
use App\Models\WorkforceDemand;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WorkforceDemandResource extends Resource
{
    protected static ?string $model = WorkforceDemand::class;
    protected static ?string $modelLabel = 'Nhu cầu nhân lực';
    protected static ?string $pluralModelLabel = 'Nhu cầu nhân lực';
    protected static ?string $navigationLabel = 'Nhu cầu nhân lực';
    protected static \UnitEnum|string|null $navigationGroup = 'Kết nối cung - cầu';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';
    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool { return Filament::auth()->user()?->isSystemAdmin() ?? false; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Nhu cầu doanh nghiệp')->columns(2)->schema([
                Select::make('employer_id')->label('Doanh nghiệp')->relationship('employer', 'name')->searchable()->preload()->required(),
                Select::make('reporting_period_id')->label('Kỳ báo cáo')->relationship('period', 'name')->searchable()->preload()->required(),
                Select::make('training_major_id')->label('Ngành / nghề')->relationship('major', 'name')->searchable()->preload()->required(),
                Select::make('education_level_id')->label('Trình độ')->relationship('trainingLevel', 'name', modifyQueryUsing: fn ($query) => $query->where('is_active', true)->orderBy('display_order'))->searchable()->preload()->required(),
                Select::make('purpose')->label('Mục đích')->required()->default('RECRUITMENT')->options([
                    'RECRUITMENT' => 'Tuyển dụng',
                    'NEW_TRAINING' => 'Đào tạo mới',
                    'RETRAINING' => 'Đào tạo lại',
                ]),
                TextInput::make('quantity')->label('Số người cần')->numeric()->minValue(1)->maxValue(100000)->required(),
                Select::make('skills')->label('Kỹ năng yêu cầu')->relationship('skills', 'name')->multiple()->searchable()->preload()->columnSpanFull(),
                TextInput::make('location')->label('Khu vực / địa điểm')->maxLength(200),
                TextInput::make('age_range')->label('Độ tuổi')->maxLength(80),
                TextInput::make('salary')->label('Thu nhập dự kiến')->maxLength(100),
                Select::make('status')->label('Trạng thái')->required()->default('DRAFT')->options([
                    'DRAFT' => 'Bản nháp',
                    'PUBLISHED' => 'Đã công bố',
                    'CLOSED' => 'Đã đóng',
                ]),
                Textarea::make('note')->label('Ghi chú / điều kiện phối hợp')->rows(4)->maxLength(2000)->columnSpanFull(),
                Toggle::make('is_sample')->label('Dữ liệu minh họa')->default(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('employer.name')->label('Doanh nghiệp')->searchable()->sortable()->weight('bold')->wrap(),
            TextColumn::make('major.name')->label('Ngành / nghề')->searchable()->wrap(),
            TextColumn::make('trainingLevel.name')->label('Trình độ')->badge(),
            TextColumn::make('period.name')->label('Kỳ')->sortable(),
            TextColumn::make('quantity')->label('Số người')->numeric()->sortable()->alignEnd(),
            TextColumn::make('status')->label('Trạng thái')->badge()->formatStateUsing(fn (string $state): string => match ($state) {
                'PUBLISHED' => 'Đã công bố', 'CLOSED' => 'Đã đóng', default => 'Bản nháp',
            })->color(fn (string $state): string => match ($state) {
                'PUBLISHED' => 'success', 'CLOSED' => 'gray', default => 'warning',
            }),
        ])->filters([
            SelectFilter::make('reporting_period_id')->label('Kỳ báo cáo')->relationship('period', 'name'),
            SelectFilter::make('training_major_id')->label('Ngành / nghề')->relationship('major', 'name')->searchable()->preload(),
            SelectFilter::make('status')->label('Trạng thái')->options(['DRAFT' => 'Bản nháp', 'PUBLISHED' => 'Đã công bố', 'CLOSED' => 'Đã đóng']),
        ])->recordActions([EditAction::make()->label('Chỉnh sửa'), DeleteAction::make()->label('Xóa')])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListWorkforceDemands::route('/'), 'create' => CreateWorkforceDemand::route('/create'), 'edit' => EditWorkforceDemand::route('/{record}/edit')];
    }
}
