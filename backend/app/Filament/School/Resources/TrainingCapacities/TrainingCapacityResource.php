<?php

namespace App\Filament\School\Resources\TrainingCapacities;

use App\Filament\School\Resources\TrainingCapacities\Pages\CreateTrainingCapacity;
use App\Filament\School\Resources\TrainingCapacities\Pages\EditTrainingCapacity;
use App\Filament\School\Resources\TrainingCapacities\Pages\ListTrainingCapacities;
use App\Models\TrainingLevel;
use App\Models\ReportingPeriod;
use App\Models\School;
use App\Models\Skill;
use App\Models\TrainingCapacity;
use App\Models\TrainingMajor;
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

class TrainingCapacityResource extends Resource
{
    protected static ?string $model = TrainingCapacity::class;
    protected static ?string $modelLabel = 'Năng lực đào tạo';
    protected static ?string $pluralModelLabel = 'Năng lực đào tạo';
    protected static ?string $navigationLabel = 'Năng lực đào tạo';
    protected static \UnitEnum|string|null $navigationGroup = 'Kết nối cung - cầu';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool { return Filament::auth()->user()?->isSchoolAdmin() ?? false; }
    public static function canEdit(Model $record): bool { return static::canAccess() && $record instanceof TrainingCapacity && in_array($record->school_id, static::allowedSchoolIds(), true); }
    public static function canDelete(Model $record): bool { return static::canEdit($record); }

    /** @return array<int, string> */
    public static function allowedSchoolIds(): array
    {
        $schoolId = (string) Filament::auth()->user()?->school_id;
        if ($schoolId === '') return [];

        return School::query()
            ->whereKey($schoolId)
            ->orWhere('parent_school_id', $schoolId)
            ->pluck('id')
            ->map(fn ($id): string => (string) $id)
            ->all();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereIn('school_id', static::allowedSchoolIds());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Khai báo năng lực đào tạo')
                ->description('Năng lực được khai báo theo ngành, trình độ và từng kỳ báo cáo.')
                ->columns(2)
                ->schema([
                    Select::make('school_id')->label('Trường / cơ sở')->required()->searchable()->options(
                        fn (): array => School::query()->whereIn('id', static::allowedSchoolIds())->orderBy('name')->pluck('name', 'id')->all(),
                    ),
                    Select::make('reporting_period_id')->label('Kỳ báo cáo')->required()->searchable()->options(
                        fn (): array => ReportingPeriod::query()->where('is_active', true)->orderByDesc('starts_at')->pluck('name', 'id')->all(),
                    ),
                    Select::make('training_major_id')->label('Ngành / nghề')->required()->searchable()->options(
                        fn (): array => TrainingMajor::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all(),
                    ),
                    Select::make('education_level_id')->label('Trình độ')->required()->options(
                        fn (): array => TrainingLevel::query()->where('is_active', true)->orderBy('display_order')->pluck('name', 'id')->all(),
                    ),
                    TextInput::make('quantity')->label('Năng lực dự kiến (người / kỳ)')->numeric()->minValue(1)->maxValue(100000)->required(),
                    TextInput::make('location')->label('Khu vực / địa điểm')->placeholder('Tên xã, phường hoặc khu vực')->maxLength(255)->required(),
                    TextInput::make('duration')->label('Thời lượng / hình thức đào tạo')->maxLength(120),
                    Select::make('skills')->label('Kỹ năng có thể đào tạo')->relationship('skills', 'name')->multiple()->searchable()->preload()->columnSpanFull(),
                    Textarea::make('equipment')->label('Đội ngũ, thiết bị và điều kiện đào tạo')->rows(3)->maxLength(2000)->columnSpanFull(),
                    Textarea::make('note')->label('Ghi chú / điều kiện phối hợp')->rows(3)->maxLength(2000)->columnSpanFull(),
                    Select::make('status')->label('Trạng thái')->required()->default('ACTIVE')->options(['ACTIVE' => 'Đang hoạt động', 'INACTIVE' => 'Ngừng hoạt động']),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('school.name')->label('Cơ sở đào tạo')->searchable()->weight('bold')->wrap(),
            TextColumn::make('major.name')->label('Ngành / nghề')->searchable()->wrap(),
            TextColumn::make('trainingLevel.name')->label('Trình độ')->badge(),
            TextColumn::make('period.name')->label('Kỳ'),
            TextColumn::make('quantity')->label('Năng lực')->numeric()->alignEnd(),
            TextColumn::make('location')->label('Địa điểm')->searchable()->wrap()->toggleable(),
            TextColumn::make('status')->label('Trạng thái')->badge()->formatStateUsing(fn (string $state): string => $state === 'ACTIVE' ? 'Hoạt động' : 'Ngừng hoạt động')->color(fn (string $state): string => $state === 'ACTIVE' ? 'success' : 'gray'),
        ])->recordActions([EditAction::make()->label('Chỉnh sửa'), DeleteAction::make()->label('Xóa')])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListTrainingCapacities::route('/'), 'create' => CreateTrainingCapacity::route('/create'), 'edit' => EditTrainingCapacity::route('/{record}/edit')];
    }
}
