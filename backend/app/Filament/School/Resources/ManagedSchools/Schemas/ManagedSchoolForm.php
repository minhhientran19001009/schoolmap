<?php

namespace App\Filament\School\Resources\ManagedSchools\Schemas;

use App\Models\School;
use App\Models\TrainingMajor;
use App\Models\Ward;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManagedSchoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('ManagedSchoolTabs')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Thông tin chung')
                            ->icon(Heroicon::OutlinedBuildingLibrary)
                            ->schema([
                                Section::make('Thông tin cơ sở')
                                    ->description('Cập nhật thông tin liên hệ, địa chỉ và vị trí của trường hoặc cơ sở trực thuộc.')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextInput::make('name')
                                                ->label('Tên trường / cơ sở')
                                                ->required()
                                                ->maxLength(255),

                                            TextInput::make('code')
                                                ->label('Mã cơ sở')
                                                ->disabled()
                                                ->dehydrated(false),

                                            Select::make('ward')
                                                ->label('Phường / Xã')
                                                ->options(fn (): array => Ward::query()
                                                    ->orderBy('full_name')
                                                    ->pluck('full_name', 'full_name')
                                                    ->all())
                                                ->searchable(),

                                            TextInput::make('phone')
                                                ->label('Số điện thoại')
                                                ->tel(),

                                            TextInput::make('email')
                                                ->label('Email')
                                                ->email(),

                                            TextInput::make('website')
                                                ->label('Website')
                                                ->url(),

                                            Select::make('educationLevels')
                                                ->label('Cấp học / danh mục đào tạo')
                                                ->relationship('educationLevels', 'name')
                                                ->multiple()
                                                ->searchable()
                                                ->preload()
                                                ->required(),

                                            TextInput::make('address')
                                                ->label('Địa chỉ chi tiết')
                                                ->columnSpan(3)
                                                ->maxLength(255),
                                        ]),

                                        Grid::make(2)->schema([
                                            TextInput::make('lat')
                                                ->label('Vĩ độ')
                                                ->numeric()
                                                ->required(),

                                            TextInput::make('lng')
                                                ->label('Kinh độ')
                                                ->numeric()
                                                ->required(),
                                        ]),

                                        Hidden::make('campus_type'),
                                        Hidden::make('parent_school_id'),
                                    ]),

                                Section::make('Hình ảnh trường / cơ sở')
                                    ->schema([
                                        FileUpload::make('gallery')
                                            ->label('Thư viện ảnh')
                                            ->image()
                                            ->multiple()
                                            ->reorderable()
                                            ->directory('schools/gallery')
                                            ->disk('public')
                                            ->visibility('public'),
                                    ]),
                            ]),

                        Tab::make('Lãnh đạo')
                            ->icon(Heroicon::OutlinedUserGroup)
                            ->schema([
                                Repeater::make('leaders')
                                    ->label('Danh sách lãnh đạo')
                                    ->schema([
                                        TextInput::make('name')->label('Họ và tên')->required(),
                                        TextInput::make('position')->label('Chức vụ')->required(),
                                    ])
                                    ->columns(2)
                                    ->collapsible()
                                    ->defaultItems(0),
                            ]),

                        Tab::make('Ngành nghề đào tạo')
                            ->icon(Heroicon::OutlinedAcademicCap)
                            ->schema([
                                Section::make('Danh mục ngành / nghề')
                                    ->description('Chọn ngành từ danh mục chung. Tài khoản nhà trường không được tự tạo danh mục mới.')
                                    ->schema([
                                        Repeater::make('majorAssignments')
                                            ->label('Ngành / nghề đào tạo')
                                            ->relationship('majorAssignments')
                                            ->schema([
                                                Select::make('training_major_id')
                                                    ->label('Chuyên ngành / nghề')
                                                    ->relationship('trainingMajor', 'name', modifyQueryUsing: fn ($query) => $query->where('is_active', true))
                                                    ->searchable()
                                                    ->getSearchResultsUsing(function (?string $search): array {
                                                        $search = mb_strtolower(trim((string) $search), 'UTF-8');

                                                        return TrainingMajor::query()
                                                            ->where('is_active', true)
                                                            ->when($search !== '', fn ($query) => $query->whereRaw(
                                                                'LOWER(name) COLLATE utf8mb4_bin LIKE ?',
                                                                ["%{$search}%"],
                                                            ))
                                                            ->orderBy('name')
                                                            ->limit(50)
                                                            ->pluck('name', 'id')
                                                            ->toArray();
                                                    })
                                                    ->optionsLimit(50)
                                                    ->required()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                                                TextInput::make('annual_quota')
                                                    ->label('Chỉ tiêu tuyển sinh')
                                                    ->numeric()
                                                    ->minValue(0)
                                                    ->default(0)
                                                    ->suffix('SV/năm'),
                                            ])
                                            ->columns(2)
                                            ->collapsible()
                                            ->defaultItems(0),
                                    ]),

                                Grid::make(3)->schema([
                                    TextInput::make('annual_enrollment')->label('Tuyển sinh hàng năm')->numeric()->default(0),
                                    TextInput::make('student_count')->label('Tổng số người học')->numeric()->default(0),
                                    TextInput::make('annual_graduates')->label('Tốt nghiệp hàng năm')->numeric()->default(0),
                                    TextInput::make('employment_rate')->label('Tỷ lệ có việc làm (%)')->numeric()->step(0.1),
                                ]),
                            ]),

                        Tab::make('Cơ sở vật chất')
                            ->icon(Heroicon::OutlinedWrenchScrewdriver)
                            ->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('campus_area_m2')->label('Diện tích khuôn viên (m²)')->numeric(),
                                    TextInput::make('classroom_count')->label('Số phòng học')->numeric()->default(0),
                                    TextInput::make('workshops_count')->label('Số xưởng / phòng thực hành')->numeric()->default(0),
                                    TextInput::make('computer_room_count')->label('Số phòng máy')->numeric()->default(0),
                                    TextInput::make('lab_count')->label('Số phòng thí nghiệm')->numeric()->default(0),
                                ]),
                            ]),
                    ]),
            ]);
    }
}
