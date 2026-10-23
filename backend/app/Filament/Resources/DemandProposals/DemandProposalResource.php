<?php

namespace App\Filament\Resources\DemandProposals;

use App\Filament\Resources\DemandProposals\Pages\EditDemandProposal;
use App\Filament\Resources\DemandProposals\Pages\ListDemandProposals;
use App\Models\DemandProposal;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DemandProposalResource extends Resource
{
    protected static ?string $model = DemandProposal::class;
    protected static ?string $modelLabel = 'Đề xuất đáp ứng';
    protected static ?string $pluralModelLabel = 'Đề xuất đáp ứng';
    protected static ?string $navigationLabel = 'Đề xuất đáp ứng';
    protected static \UnitEnum|string|null $navigationGroup = 'Kết nối cung - cầu';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?int $navigationSort = 6;

    public static function canAccess(): bool { return Filament::auth()->user()?->isSystemAdmin() ?? false; }
    public static function canCreate(): bool { return false; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Đề xuất của nhà trường')->columns(2)->schema([
                Select::make('workforce_demand_id')->label('Nhu cầu')->relationship('demand', 'id')->disabled(),
                Select::make('training_capacity_id')->label('Năng lực')->relationship('capacity', 'id')->disabled(),
                TextInput::make('quantity')->label('Số người đề xuất')->numeric()->disabled(),
                Select::make('status')->label('Trạng thái')->required()->options([
                    'SUBMITTED' => 'Đã gửi',
                    'WITHDRAWN' => 'Đã rút lại',
                ]),
                Textarea::make('plan')->label('Phương án đáp ứng')->rows(5)->disabled()->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('demand.employer.name')->label('Doanh nghiệp')->searchable()->weight('bold')->wrap(),
            TextColumn::make('demand.major.name')->label('Ngành / nghề')->searchable()->wrap(),
            TextColumn::make('capacity.school.name')->label('Cơ sở đề xuất')->searchable()->wrap(),
            TextColumn::make('quantity')->label('Số người')->numeric()->sortable()->alignEnd(),
            TextColumn::make('status')->label('Trạng thái')->badge()->formatStateUsing(fn (string $state): string => $state === 'WITHDRAWN' ? 'Đã rút lại' : 'Đã gửi')->color(fn (string $state): string => $state === 'WITHDRAWN' ? 'gray' : 'info'),
            TextColumn::make('created_at')->label('Thời gian')->dateTime('d/m/Y H:i')->sortable(),
        ])->filters([
            SelectFilter::make('status')->label('Trạng thái')->options(['SUBMITTED' => 'Đã gửi', 'WITHDRAWN' => 'Đã rút lại']),
        ])->recordActions([EditAction::make()->label('Cập nhật trạng thái')])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => ListDemandProposals::route('/'), 'edit' => EditDemandProposal::route('/{record}/edit')];
    }
}
