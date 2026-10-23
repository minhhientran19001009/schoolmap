<?php

namespace App\Filament\Business\Resources\ReceivedProposals;

use App\Filament\Business\Resources\ReceivedProposals\Pages\ListReceivedProposals;
use App\Models\DemandProposal;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ReceivedProposalResource extends Resource
{
    protected static ?string $model = DemandProposal::class;
    protected static ?string $modelLabel = 'Đề xuất từ nhà trường';
    protected static ?string $pluralModelLabel = 'Đề xuất từ nhà trường';
    protected static ?string $navigationLabel = 'Đề xuất từ nhà trường';
    protected static \UnitEnum|string|null $navigationGroup = 'Kết nối cung - cầu';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool { return Filament::auth()->user()?->isBusinessAdmin() ?? false; }
    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('demand', fn (Builder $query) => $query->where('employer_id', Filament::auth()->user()?->employer_id))
            ->with(['demand.major', 'capacity.school']);
    }
    public static function form(Schema $schema): Schema { return $schema->components([]); }
    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('demand.major.name')->label('Ngành / nghề')->searchable()->weight('bold')->wrap(),
            TextColumn::make('capacity.school.name')->label('Cơ sở đề xuất')->searchable()->wrap(),
            TextColumn::make('quantity')->label('Số người')->numeric()->alignEnd(),
            TextColumn::make('plan')->label('Phương án đáp ứng')->wrap()->limit(120)->tooltip(fn (DemandProposal $record): string => $record->plan),
            TextColumn::make('status')->label('Trạng thái')->badge()->formatStateUsing(fn (string $state): string => $state === 'WITHDRAWN' ? 'Đã rút lại' : 'Đã gửi')->color(fn (string $state): string => $state === 'WITHDRAWN' ? 'gray' : 'info'),
            TextColumn::make('created_at')->label('Thời gian')->dateTime('d/m/Y H:i'),
        ])->defaultSort('created_at', 'desc');
    }
    public static function getPages(): array { return ['index' => ListReceivedProposals::route('/')]; }
}
