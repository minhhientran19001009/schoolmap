<?php

namespace App\Filament\Resources\Skills;

use App\Filament\Resources\Skills\Pages\CreateSkill;
use App\Filament\Resources\Skills\Pages\EditSkill;
use App\Filament\Resources\Skills\Pages\ListSkills;
use App\Models\CareerField;
use App\Models\Skill;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SkillResource extends Resource
{
    protected static ?string $model = Skill::class;
    protected static ?string $modelLabel = 'Kỹ năng';
    protected static ?string $pluralModelLabel = 'Danh mục kỹ năng';
    protected static ?string $navigationLabel = 'Kỹ năng';
    protected static \UnitEnum|string|null $navigationGroup = 'Kết nối cung - cầu';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?int $navigationSort = 4;

    protected static bool $shouldRegisterNavigation = false;

    public static function canAccess(): bool { return false; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thông tin kỹ năng')
                ->columnSpanFull()
                ->columns(['default' => 1, 'md' => 2])
                ->schema([
                    TextInput::make('name')
                        ->label('Tên kỹ năng')
                        ->required()
                        ->maxLength(120)
                        ->rule(fn (?Skill $record): \Closure => function (string $attribute, mixed $value, \Closure $fail) use ($record): void {
                            $normalized = Skill::normalize((string) $value);
                            $exists = Skill::query()
                                ->where('normalized_name', $normalized)
                                ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                                ->exists();
                            if ($exists) {
                                $fail('Kỹ năng này đã tồn tại trong danh mục.');
                            }
                        }),
                    Select::make('career_field')
                        ->label('Lĩnh vực nghề nghiệp')
                        ->options(fn (): array => CareerField::options(onlyActive: true))
                        ->searchable()
                        ->placeholder('Chọn lĩnh vực nghề nghiệp'),
                    Toggle::make('is_active')->label('Đang sử dụng')->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('careerField'))
            ->columns([
                TextColumn::make('name')->label('Tên kỹ năng')->searchable()->sortable()->weight('bold'),
                TextColumn::make('career_field')
                    ->label('Lĩnh vực nghề nghiệp')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (?string $state, Skill $record): string => $record->careerField?->name ?? ($state ? (Skill::CAREER_FIELDS[$state] ?? $state) : 'Chung / Chưa phân loại'))
                    ->sortable()
                    ->searchable(),
                IconColumn::make('is_active')->label('Đang dùng')->boolean(),
            ])
            ->filters([
                SelectFilter::make('career_field')->label('Lĩnh vực nghề nghiệp')->options(fn (): array => CareerField::options()),
                SelectFilter::make('is_active')->label('Trạng thái')->options(['1' => 'Đang dùng', '0' => 'Ngừng dùng']),
            ])
            ->recordActions([EditAction::make()->label('Chỉnh sửa'), DeleteAction::make()->label('Xóa')])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Xóa các mục đã chọn'),
                ]),
            ])
            ->defaultSort('name')
            ->defaultPaginationPageOption(25);
    }

    public static function getPages(): array
    {
        return ['index' => ListSkills::route('/'), 'create' => CreateSkill::route('/create'), 'edit' => EditSkill::route('/{record}/edit')];
    }
}
