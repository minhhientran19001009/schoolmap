<?php

namespace App\Filament\Resources\Employers;

use App\Filament\Resources\Employers\Pages\CreateEmployer;
use App\Filament\Resources\Employers\Pages\EditEmployer;
use App\Filament\Resources\Employers\Pages\ListEmployers;
use App\Models\Employer;
use App\Models\Ward;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EmployerResource extends Resource
{
    protected static ?string $model = Employer::class;
    protected static ?string $modelLabel = 'Doanh nghiệp';
    protected static ?string $pluralModelLabel = 'Danh sách doanh nghiệp';
    protected static ?string $navigationLabel = 'Doanh nghiệp';
    protected static \UnitEnum|string|null $navigationGroup = 'Kết nối cung - cầu';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return Filament::auth()->user()?->isSystemAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thông tin doanh nghiệp')->columns(2)->schema([
                TextInput::make('name')->label('Tên doanh nghiệp')->required()->maxLength(200)->columnSpanFull(),
                TextInput::make('tax_code')->label('Mã số thuế')->unique(ignoreRecord: true)->maxLength(30),
                TextInput::make('industry')->label('Lĩnh vực hoạt động')->maxLength(150),
                TextInput::make('region')->label('Khu vực')->maxLength(150),
                TextInput::make('employee_count')->label('Quy mô lao động tham khảo')->numeric()->minValue(0),
                Select::make('ward_id')->label('Phường / Xã')->searchable()->options(
                    fn (): array => Ward::query()->orderBy('name')->pluck('full_name', 'id')->all(),
                ),
                TextInput::make('address')->label('Địa chỉ')->maxLength(255)->columnSpanFull(),
                TextInput::make('contact_person')->label('Người liên hệ')->maxLength(120),
                TextInput::make('phone')->label('Số điện thoại')->tel()->maxLength(50),
                TextInput::make('email')->label('Email')->email()->maxLength(120),
                TextInput::make('website')->label('Website')->url()->maxLength(255),
                TextInput::make('source_code')->label('Mã trong nguồn dữ liệu')->maxLength(50),
                Textarea::make('scope')->label('Phạm vi số liệu')->rows(3)->columnSpanFull(),
                Textarea::make('data_note')->label('Lưu ý dữ liệu')->rows(3)->columnSpanFull(),
                Select::make('status')->label('Trạng thái')->required()->default('ACTIVE')->options([
                    'ACTIVE' => 'Đang hoạt động',
                    'INACTIVE' => 'Ngừng hoạt động',
                ]),
                Toggle::make('is_verified')->label('Đã xác minh')->default(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Doanh nghiệp')->searchable()->sortable()->weight('bold')->wrap(),
            TextColumn::make('tax_code')->label('Mã số thuế')->searchable()->placeholder('Chưa cập nhật'),
            TextColumn::make('industry')->label('Lĩnh vực')->searchable()->wrap()->placeholder('Chưa cập nhật'),
            TextColumn::make('employee_count')->label('Lao động tham khảo')->numeric()->sortable()->placeholder('—')->alignEnd(),
            TextColumn::make('demands_count')->label('Nhu cầu')->counts('demands')->badge()->alignEnd(),
            TextColumn::make('status')->label('Trạng thái')->badge()->formatStateUsing(
                fn (string $state): string => $state === 'ACTIVE' ? 'Hoạt động' : 'Ngừng hoạt động',
            )->color(fn (string $state): string => $state === 'ACTIVE' ? 'success' : 'gray'),
            IconColumn::make('is_verified')->label('Xác minh')->boolean(),
        ])->filters([
            SelectFilter::make('status')->label('Trạng thái')->options([
                'ACTIVE' => 'Đang hoạt động',
                'INACTIVE' => 'Ngừng hoạt động',
            ]),
        ])->recordActions([
            EditAction::make()->label('Chỉnh sửa'),
            DeleteAction::make()->label('Xóa'),
        ])->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployers::route('/'),
            'create' => CreateEmployer::route('/create'),
            'edit' => EditEmployer::route('/{record}/edit'),
        ];
    }
}
