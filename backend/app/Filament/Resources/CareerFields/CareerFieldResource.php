<?php

namespace App\Filament\Resources\CareerFields;

use App\Filament\Resources\CareerFields\Pages\CreateCareerField;
use App\Filament\Resources\CareerFields\Pages\EditCareerField;
use App\Filament\Resources\CareerFields\Pages\ListCareerFields;
use App\Models\CareerField;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CareerFieldResource extends Resource
{
    protected static ?string $model = CareerField::class;

    protected static ?string $modelLabel = 'Lĩnh vực công việc';

    protected static ?string $pluralModelLabel = 'Danh mục lĩnh vực công việc';

    protected static ?string $navigationLabel = 'Lĩnh vực công việc';

    protected static \UnitEnum|string|null $navigationGroup = 'Kết nối cung - cầu';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return Filament::auth()->user()?->isSystemAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thông tin lĩnh vực công việc')
                ->columnSpanFull()
                ->schema([
                    TextInput::make('name')
                        ->label('Tên lĩnh vực')
                        ->required()
                        ->maxLength(150)
                        ->unique(ignoreRecord: true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên lĩnh vực')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),
            ])
            ->recordActions([
                EditAction::make()->label('Chỉnh sửa'),
                DeleteAction::make()->label('Xóa'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xóa các mục đã chọn'),
                ]),
            ])
            ->defaultSort('display_order')
            ->defaultPaginationPageOption(25);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCareerFields::route('/'),
            'create' => CreateCareerField::route('/create'),
            'edit' => EditCareerField::route('/{record}/edit'),
        ];
    }
}
