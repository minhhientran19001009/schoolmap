<?php

namespace App\Filament\Resources\TrainingCapacities;

use App\Filament\Resources\TrainingCapacities\Pages\CreateTrainingCapacity;
use App\Filament\Resources\TrainingCapacities\Pages\EditTrainingCapacity;
use App\Filament\Resources\TrainingCapacities\Pages\ListTrainingCapacities;
use App\Models\TrainingCapacity;
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

class TrainingCapacityResource extends Resource
{
    protected static ?string $model = TrainingCapacity::class;
    protected static ?string $modelLabel = 'Năng lực đào tạo';
    protected static ?string $pluralModelLabel = 'Năng lực đào tạo';
    protected static ?string $navigationLabel = 'Năng lực đào tạo';
    protected static \UnitEnum|string|null $navigationGroup = 'Kết nối cung - cầu';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool { return Filament::auth()->user()?->isSystemAdmin() ?? false; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Năng lực của cơ sở đào tạo')->columns(2)->schema([
                Select::make('school_id')->label('Trường / cơ sở')->relationship('school', 'name')->searchable()->preload()->required(),
                Select::make('reporting_period_id')->label('Kỳ báo cáo')->relationship('period', 'name')->searchable()->preload()->required(),
                Select::make('training_major_id')->label('Ngành / nghề')->relationship('major', 'name')->searchable()->preload()->required(),
                Select::make('education_level_id')->label('Trình độ')->relationship('trainingLevel', 'name', modifyQueryUsing: fn ($query) => $query->where('is_active', true)->orderBy('display_order'))->searchable()->preload()->required(),
                TextInput::make('quantity')->label('Năng lực dự kiến')->numeric()->minValue(1)->maxValue(100000)->required(),
                TextInput::make('location')->label('Khu vực / địa điểm')->placeholder('Tên xã, phường hoặc khu vực')->maxLength(255)->required(),
                TextInput::make('duration')->label('Thời lượng / hình thức')->maxLength(120),
                Select::make('skills')->label('Kỹ năng có thể đào tạo')->relationship('skills', 'name')->multiple()->searchable()->preload()->columnSpanFull(),
                Textarea::make('equipment')->label('Đội ngũ, thiết bị và điều kiện đào tạo')->rows(3)->maxLength(2000)->columnSpanFull(),
                Textarea::make('note')->label('Ghi chú')->rows(3)->maxLength(2000)->columnSpanFull(),
                Select::make('status')->label('Trạng thái')->required()->default('ACTIVE')->options(['ACTIVE' => 'Đang hoạt động', 'INACTIVE' => 'Ngừng hoạt động']),
                Toggle::make('is_sample')->label('Dữ liệu minh họa')->default(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('school.name')->label('Cơ sở đào tạo')->searchable()->sortable()->weight('bold')->wrap(),
            TextColumn::make('major.name')->label('Ngành / nghề')->searchable()->wrap(),
            TextColumn::make('trainingLevel.name')->label('Trình độ')->badge(),
            TextColumn::make('period.name')->label('Kỳ')->sortable(),
            TextColumn::make('quantity')->label('Năng lực')->numeric()->sortable()->alignEnd(),
            TextColumn::make('location')->label('Địa điểm')->searchable()->wrap()->toggleable(),
            TextColumn::make('status')->label('Trạng thái')->badge()->formatStateUsing(fn (string $state): string => $state === 'ACTIVE' ? 'Hoạt động' : 'Ngừng hoạt động')->color(fn (string $state): string => $state === 'ACTIVE' ? 'success' : 'gray'),
        ])->filters([
            SelectFilter::make('reporting_period_id')->label('Kỳ báo cáo')->relationship('period', 'name'),
            SelectFilter::make('training_major_id')->label('Ngành / nghề')->relationship('major', 'name')->searchable()->preload(),
        ])->recordActions([EditAction::make(), DeleteAction::make()])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListTrainingCapacities::route('/'), 'create' => CreateTrainingCapacity::route('/create'), 'edit' => EditTrainingCapacity::route('/{record}/edit')];
    }
}
