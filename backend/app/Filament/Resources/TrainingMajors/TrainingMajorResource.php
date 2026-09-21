<?php

namespace App\Filament\Resources\TrainingMajors;

use App\Filament\Resources\TrainingMajors\Pages\CreateTrainingMajor;
use App\Filament\Resources\TrainingMajors\Pages\EditTrainingMajor;
use App\Filament\Resources\TrainingMajors\Pages\ListTrainingMajors;
use App\Filament\Resources\TrainingMajors\Schemas\TrainingMajorForm;
use App\Filament\Resources\TrainingMajors\Tables\TrainingMajorsTable;
use App\Models\TrainingMajor;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TrainingMajorResource extends Resource
{
    protected static ?string $model = TrainingMajor::class;

    protected static ?string $modelLabel = 'Chuyên ngành đào tạo';
    protected static ?string $pluralModelLabel = 'Danh mục Chuyên ngành đào tạo';
    protected static ?string $navigationLabel = 'Chuyên ngành đào tạo';
    protected static \UnitEnum|string|null $navigationGroup = 'Mạng lưới Giáo dục';
    protected static ?int $navigationSort = 2;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedListBullet;

    public static function form(Schema $schema): Schema
    {
        return TrainingMajorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingMajorsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingMajors::route('/'),
            'create' => CreateTrainingMajor::route('/create'),
            'edit' => EditTrainingMajor::route('/{record}/edit'),
        ];
    }
}
