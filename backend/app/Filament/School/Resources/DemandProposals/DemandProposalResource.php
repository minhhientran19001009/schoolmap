<?php

namespace App\Filament\School\Resources\DemandProposals;

use App\Filament\School\Resources\DemandProposals\Pages\CreateDemandProposal;
use App\Filament\School\Resources\DemandProposals\Pages\ListDemandProposals;
use App\Filament\School\Resources\TrainingCapacities\TrainingCapacityResource;
use App\Models\DemandProposal;
use App\Models\TrainingCapacity;
use App\Models\WorkforceDemand;
use Filament\Actions\Action;
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

class DemandProposalResource extends Resource
{
    protected static ?string $model = DemandProposal::class;
    protected static ?string $modelLabel = 'Đề xuất đáp ứng';
    protected static ?string $pluralModelLabel = 'Đối chiếu và đề xuất';
    protected static ?string $navigationLabel = 'Đối chiếu và đề xuất';
    protected static \UnitEnum|string|null $navigationGroup = 'Kết nối cung - cầu';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool { return Filament::auth()->user()?->isSchoolAdmin() ?? false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('capacity', fn (Builder $query) => $query->whereIn('school_id', TrainingCapacityResource::allowedSchoolIds()));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Đề xuất đáp ứng nhu cầu')->description('Đề xuất được lưu để phối hợp, chưa phải cam kết cung ứng.')->schema([
                Select::make('workforce_demand_id')->label('Nhu cầu doanh nghiệp')->required()->searchable()->options(
                    fn (): array => WorkforceDemand::query()->where('status', 'PUBLISHED')->with(['employer', 'major', 'trainingLevel', 'period'])->get()->mapWithKeys(
                        fn (WorkforceDemand $demand): array => [$demand->id => "{$demand->employer?->name} · {$demand->major?->name} · {$demand->trainingLevel?->name} · {$demand->period?->name}"],
                    )->all(),
                ),
                Select::make('training_capacity_id')->label('Năng lực đào tạo đáp ứng')->required()->searchable()->options(
                    fn (): array => TrainingCapacity::query()->whereIn('school_id', TrainingCapacityResource::allowedSchoolIds())->where('status', 'ACTIVE')->with(['school', 'major', 'trainingLevel', 'period'])->get()->mapWithKeys(
                        fn (TrainingCapacity $capacity): array => [$capacity->id => "{$capacity->school?->name} · {$capacity->major?->name} · {$capacity->trainingLevel?->name} · {$capacity->period?->name}"],
                    )->all(),
                ),
                TextInput::make('quantity')->label('Số người đề xuất')->numeric()->minValue(1)->maxValue(100000)->required(),
                Textarea::make('plan')->label('Phương án đáp ứng')->rows(5)->minLength(10)->maxLength(2000)->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('demand.employer.name')->label('Doanh nghiệp')->searchable()->weight('bold')->wrap(),
            TextColumn::make('demand.major.name')->label('Ngành / nghề')->wrap(),
            TextColumn::make('capacity.school.name')->label('Cơ sở đáp ứng')->wrap(),
            TextColumn::make('quantity')->label('Số người')->numeric()->alignEnd(),
            TextColumn::make('status')->label('Trạng thái')->badge()->formatStateUsing(fn (string $state): string => $state === 'WITHDRAWN' ? 'Đã rút lại' : 'Đã gửi')->color(fn (string $state): string => $state === 'WITHDRAWN' ? 'gray' : 'info'),
            TextColumn::make('created_at')->label('Thời gian')->dateTime('d/m/Y H:i'),
        ])->recordActions([
            Action::make('withdraw')->label('Rút đề xuất')->color('danger')->requiresConfirmation()->visible(fn (DemandProposal $record): bool => $record->status === 'SUBMITTED')->action(fn (DemandProposal $record) => $record->update(['status' => 'WITHDRAWN'])),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListDemandProposals::route('/'), 'create' => CreateDemandProposal::route('/create')];
    }
}
