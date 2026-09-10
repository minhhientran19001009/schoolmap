<?php

namespace App\Filament\Resources\Schools\Schemas;

use App\Models\Ward;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SchoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('SchoolTabs')
                    ->columnSpanFull()
                    ->tabs([
                        // ==============================================================
                        // TAB 1: THÔNG TIN CHUNG (Bao gồm Cơ sở vật chất)
                        // ==============================================================
                        Tab::make('Thông tin chung')
                            ->icon(Heroicon::OutlinedBuildingLibrary)
                            ->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('name')
                                        ->label('Tên Cơ sở Giáo dục')
                                        ->placeholder('VD: Trường Cao đẳng Cơ giới Ninh Bình')
                                        ->required()
                                        ->columnSpan(['default' => 3, 'lg' => 1]),

                                    TextInput::make('phone')
                                        ->label('Số điện thoại liên hệ')
                                        ->tel()
                                        ->placeholder('VD: 0229 387 1042')
                                        ->columnSpan(['default' => 3, 'lg' => 1]),

                                    TextInput::make('website')
                                        ->label('Trang web / Cổng thông tin điện tử')
                                        ->url()
                                        ->placeholder('VD: https://cdyteninhbinh.edu.vn')
                                        ->prefixIcon('heroicon-m-globe-alt')
                                        ->columnSpan(['default' => 3, 'lg' => 1]),
                                ]),

                                Grid::make(2)->schema([
                                    Select::make('educationLevels')
                                        ->label('Cấp học / Danh mục đào tạo')
                                        ->relationship('educationLevels', 'name')
                                        ->multiple()
                                        ->preload()
                                        ->placeholder('Chọn một hoặc nhiều cấp học / cơ sở')
                                        ->required(),

                                    Select::make('ward')
                                        ->label('Thuộc Phường / Xã')
                                        ->options(function () {
                                            return Ward::orderBy('full_name')->pluck('full_name', 'full_name')->toArray();
                                        })
                                        ->searchable()
                                        ->placeholder('Chọn Phường / Xã')
                                        ->extraAttributes(['id' => 'school-ward-select']),
                                ]),

                                // Bộ chọn vị trí trên bản đồ Leaflet
                                View::make('filament.forms.components.location-picker')
                                    ->columnSpanFull()
                                    ->viewData(fn ($record = null) => [
                                        'initialLat' => $record?->lat ?? 20.2506,
                                        'initialLng' => $record?->lng ?? 105.9745,
                                    ]),

                                Hidden::make('lat')
                                    ->default(20.2506)
                                    ->extraAttributes(['id' => 'school-lat-input']),

                                Hidden::make('lng')
                                    ->default(105.9745)
                                    ->extraAttributes(['id' => 'school-lng-input']),

                                Grid::make(1)->schema([
                                    TextInput::make('address')
                                        ->label('Địa chỉ chi tiết')
                                        ->placeholder('Địa chỉ sẽ tự động điền khi chọn vị trí trên bản đồ, hoặc có thể chỉnh sửa thủ công')
                                        ->extraInputAttributes(['id' => 'school-address-input']),
                                ]),

                                // Cơ sở vật chất & Thư viện Hình ảnh nằm trong Thông tin chung
                                Section::make('Cơ sở vật chất & Thư viện Hình ảnh')
                                    ->description('Quy mô phòng học, xưởng thực hành và hình ảnh khuôn viên trường học')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextInput::make('campus_area_m2')
                                                ->label('Diện tích khuôn viên (m²)')
                                                ->numeric()
                                                ->placeholder('VD: 25000'),

                                            TextInput::make('classroom_count')
                                                ->label('Số phòng học / Giảng đường')
                                                ->numeric()
                                                ->default(0),

                                            TextInput::make('workshops_count')
                                                ->label('Số xưởng thực hành / Lab')
                                                ->numeric()
                                                ->default(0),
                                        ]),

                                        FileUpload::make('gallery')
                                            ->label('Thư viện ảnh trường học (Khuôn viên, giảng đường, xưởng thực hành, KTX...)')
                                            ->helperText('Tải lên nhiều ảnh định dạng JPG/PNG. Nhấp vào ảnh ngoài client sẽ bật Modal Slider xem phóng to.')
                                            ->image()
                                            ->multiple()
                                            ->reorderable()
                                            ->directory('schools/gallery')
                                            ->disk('public')
                                            ->visibility('public')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // ==============================================================
                        // TAB 2: BAN GIÁM HIỆU
                        // ==============================================================
                        Tab::make('Ban Giám hiệu')
                            ->icon(Heroicon::OutlinedUserGroup)
                            ->schema([
                                Section::make('Danh sách Ban Giám hiệu, Ban Giám đốc & Trưởng khoa')
                                    ->description('Chỉ cần nhập Họ tên và Chức vụ tương ứng (Hiệu trưởng, Hiệu phó, Trưởng khoa...)')
                                    ->schema([
                                        Repeater::make('leaders')
                                            ->label('Danh sách Ban Giám hiệu & Lãnh đạo')
                                            ->addActionLabel('+ Thêm cán bộ lãnh đạo / Trưởng khoa')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Họ và tên')
                                                    ->placeholder('VD: Nguyễn Văn A')
                                                    ->required(),

                                                TextInput::make('position')
                                                    ->label('Chức vụ')
                                                    ->placeholder('VD: Hiệu trưởng, Phó Hiệu trưởng, Trưởng khoa Cơ điện...')
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0)
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => 
                                                filled($state['name'] ?? null) 
                                                    ? (($state['position'] ?? 'Cán bộ') . ': ' . $state['name']) 
                                                    : 'Cán bộ mới'
                                            ),
                                    ]),
                            ]),

                        // ==============================================================
                        // TAB 3: ĐÀO TẠO
                        // ==============================================================
                        Tab::make('Đào tạo')
                            ->icon(Heroicon::OutlinedAcademicCap)
                            ->schema([
                                Section::make('Các ngành / nghề đào tạo')
                                    ->description('Khai báo danh mục các chuyên ngành trường đang tuyển sinh và đào tạo')
                                    ->schema([
                                        Repeater::make('training_majors')
                                            ->label('Danh mục Ngành / Nghề đào tạo')
                                            ->addActionLabel('+ Thêm ngành nghề đào tạo')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Tên ngành / nghề đào tạo')
                                                    ->placeholder('VD: Công nghệ Ô tô, May thời trang...')
                                                    ->required()
                                                    ->columnSpan(2),

                                                Select::make('degree_level')
                                                    ->label('Trình độ đào tạo')
                                                    ->options([
                                                        'cao_dang' => 'Cao đẳng',
                                                        'trung_cap' => 'Trung cấp',
                                                        'so_cap' => 'Sơ cấp / Chứng chỉ nghề',
                                                        'dai_hoc' => 'Đại học',
                                                    ])
                                                    ->default('cao_dang')
                                                    ->required(),

                                                TextInput::make('major_code')
                                                    ->label('Mã ngành (tùy chọn)')
                                                    ->placeholder('VD: 6510216'),

                                                TextInput::make('annual_quota')
                                                    ->label('Chỉ tiêu tuyển sinh')
                                                    ->numeric()
                                                    ->placeholder('VD: 120'),
                                            ])
                                            ->columns(5)
                                            ->defaultItems(0)
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => 
                                                filled($state['name'] ?? null) 
                                                    ? ($state['name'] . ' (' . ($state['degree_level'] ?? '') . ')') 
                                                    : 'Ngành đào tạo mới'
                                            ),
                                    ]),

                                Section::make('Quy mô Sinh viên & Hiệu quả đào tạo')
                                    ->description('Các chỉ số sinh viên tuyển sinh, đang học, tốt nghiệp và tỷ lệ việc làm')
                                    ->schema([
                                        Grid::make(4)->schema([
                                            TextInput::make('annual_enrollment')
                                                ->label('Tuyển sinh hàng năm')
                                                ->numeric()
                                                ->default(0)
                                                ->placeholder('Số lượng SV mới'),

                                            TextInput::make('student_count')
                                                ->label('Tổng sinh viên đang học')
                                                ->numeric()
                                                ->default(0)
                                                ->placeholder('Quy mô hiện tại'),

                                            TextInput::make('annual_graduates')
                                                ->label('Tốt nghiệp hàng năm')
                                                ->numeric()
                                                ->default(0)
                                                ->placeholder('Số ra trường/năm'),

                                            TextInput::make('employment_rate')
                                                ->label('Tỷ lệ có việc làm (%)')
                                                ->numeric()
                                                ->step(0.1)
                                                ->suffix('%')
                                                ->placeholder('VD: 92.5'),
                                        ]),
                                    ]),
                            ]),

                        // ==============================================================
                        // TAB 4: GIẢNG VIÊN
                        // ==============================================================
                        Tab::make('Giảng viên')
                            ->icon(Heroicon::OutlinedIdentification)
                            ->schema([
                                Section::make('Quy mô & Tình trạng Thừa / Thiếu giáo viên')
                                    ->description('Quản lý định biên và cân đối nhân lực giảng dạy theo quy định của Sở')
                                    ->schema([
                                        Grid::make(4)->schema([
                                            TextInput::make('teacher_count')
                                                ->label('Tổng số GV/giảng viên')
                                                ->numeric()
                                                ->default(0),

                                            TextInput::make('teacher_quota')
                                                ->label('Định biên được giao')
                                                ->numeric()
                                                ->default(0),

                                            TextInput::make('teachers_shortage')
                                                ->label('Số lượng còn THIẾU')
                                                ->numeric()
                                                ->default(0)
                                                ->extraInputAttributes(['style' => 'color: #dc2626; font-weight: bold;']),

                                            TextInput::make('teachers_surplus')
                                                ->label('Số lượng DÔI DƯ (thừa)')
                                                ->numeric()
                                                ->default(0),
                                        ]),
                                    ]),

                                Section::make('Phân loại Giảng viên theo Ngạch / Hạng')
                                    ->description('Phân loại theo tiêu chuẩn chức danh nghề nghiệp viên chức giảng dạy')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextInput::make('faculty_rank_1')
                                                ->label('Giảng viên Loại 1 (Hạng I - Cao cấp)')
                                                ->numeric()
                                                ->default(0)
                                                ->placeholder('VD: 10'),

                                            TextInput::make('faculty_rank_2')
                                                ->label('Giảng viên Loại 2 (Chính - Hạng II)')
                                                ->numeric()
                                                ->default(0)
                                                ->placeholder('VD: 20'),

                                            TextInput::make('faculty_rank_3')
                                                ->label('Giảng viên Hạng III / Giảng viên')
                                                ->numeric()
                                                ->default(0),
                                        ]),
                                    ]),

                                Section::make('Phân loại theo Học vị / Học hàm')
                                    ->description('Số lượng cán bộ giảng dạy có học vị sau đại học')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextInput::make('faculty_doctors')
                                                ->label('Tiến sĩ (TS)')
                                                ->numeric()
                                                ->default(0)
                                                ->placeholder('VD: 11'),

                                            TextInput::make('faculty_masters')
                                                ->label('Thạc sĩ (ThS)')
                                                ->numeric()
                                                ->default(0)
                                                ->placeholder('VD: 11'),

                                            TextInput::make('faculty_professors')
                                                ->label('Giáo sư / Phó Giáo sư (GS / PGS)')
                                                ->numeric()
                                                ->default(0),
                                        ]),
                                    ]),
                            ]),

                        // ==============================================================
                        // TAB 5: DOANH NGHIỆP
                        // ==============================================================
                        Tab::make('Doanh nghiệp')
                            ->icon(Heroicon::OutlinedBriefcase)
                            ->schema([
                                Section::make('Doanh nghiệp Liên kết (Slider & Modal đối tác)')
                                    ->description('Khai báo các doanh nghiệp hợp tác thực tập, tuyển dụng. Chọn "Hiện trên Slider" để hiển thị 4-5 doanh nghiệp tiêu biểu.')
                                    ->schema([
                                        Repeater::make('partner_enterprises')
                                            ->label('Danh sách Doanh nghiệp liên kết')
                                            ->addActionLabel('+ Thêm doanh nghiệp liên kết')
                                            ->schema([
                                                TextInput::make('name')
                                                    ->label('Tên doanh nghiệp')
                                                    ->placeholder('VD: Tập đoàn Hyundai Thành Công Ninh Bình')
                                                    ->required()
                                                    ->columnSpan(2),

                                                TextInput::make('cooperation')
                                                    ->label('Nội dung hợp tác')
                                                    ->placeholder('VD: Thực tập sinh, Tuyển dụng kỹ sư...')
                                                    ->columnSpan(2),

                                                FileUpload::make('logo')
                                                    ->label('Logo doanh nghiệp')
                                                    ->image()
                                                    ->directory('enterprises/logos')
                                                    ->disk('public')
                                                    ->visibility('public'),

                                                Toggle::make('is_featured')
                                                    ->label('Hiện trên Slider (Top 4-5)')
                                                    ->default(true)
                                                    ->inline(false),
                                            ])
                                            ->columns(6)
                                            ->defaultItems(0)
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Doanh nghiệp đối tác'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
