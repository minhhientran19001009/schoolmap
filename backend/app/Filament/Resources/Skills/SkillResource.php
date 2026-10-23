<?php

namespace App\Filament\Resources\Skills;

use App\Filament\Resources\Skills\Pages\CreateSkill;
use App\Filament\Resources\Skills\Pages\EditSkill;
use App\Filament\Resources\Skills\Pages\ListSkills;
use App\Models\Skill;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SkillResource extends Resource
{
    protected static ?string $model = Skill::class;
    protected static ?string $modelLabel = 'Kỹ năng';
    protected static ?string $pluralModelLabel = 'Danh mục kỹ năng';
    protected static ?string $navigationLabel = 'Kỹ năng';
    protected static \UnitEnum|string|null $navigationGroup = 'Kết nối cung - cầu';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool { return Filament::auth()->user()?->isSystemAdmin() ?? false; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thông tin kỹ năng')->schema([
                TextInput::make('name')->label('Tên kỹ năng')->required()->maxLength(120),
                Toggle::make('is_active')->label('Đang sử dụng')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Tên kỹ năng')->searchable()->sortable()->weight('bold'),
            IconColumn::make('is_active')->label('Đang dùng')->boolean(),
        ])->recordActions([EditAction::make(), DeleteAction::make()])->defaultSort('name');
    }

    public static function getPages(): array
    {
        return ['index' => ListSkills::route('/'), 'create' => CreateSkill::route('/create'), 'edit' => EditSkill::route('/{record}/edit')];
    }
}
