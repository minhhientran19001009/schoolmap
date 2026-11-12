<?php

namespace App\Filament\Resources\JobPostings;

use App\Models\CareerField;
use App\Models\JobPosting;
use App\Services\JobPostingExcelService;
use App\Support\SkillList;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class JobPostingResource extends Resource
{
    protected static ?string $model = JobPosting::class;

    protected static ?string $modelLabel = 'Tin việc làm';

    protected static ?string $pluralModelLabel = 'Tin việc làm';

    protected static ?string $navigationLabel = 'Tin việc làm';

    protected static \UnitEnum|string|null $navigationGroup = 'Kết nối cung - cầu';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return Filament::auth()->user()?->isSystemAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        $contentFields = [];
        foreach (['description' => 'Mô tả công việc', 'requirements' => 'Yêu cầu', 'benefits' => 'Quyền lợi', 'skills' => 'Kỹ năng'] as $key => $label) {
            $contentFields[] = Textarea::make('details.'.$key)->label($label)->helperText($key === 'skills' ? 'Phân cách kỹ năng bằng dấu phẩy hoặc xuống dòng' : 'Mỗi ý một dòng')->rows($key === 'skills' ? 4 : 6)
                ->formatStateUsing(fn ($state) => is_array($state) ? implode("\n", $state) : $state)
                ->dehydrateStateUsing(fn ($state) => $key === 'skills' ? SkillList::parse($state) : array_values(array_filter(array_map('trim', preg_split('/[\r\n]+/u', (string) $state)))));
        }

        return $schema->components([
            Section::make('Thông tin tuyển dụng')
                ->columnSpanFull()
                ->columns(['default' => 1, 'md' => 2, 'xl' => 3])
                ->schema([
                    TextInput::make('title')->label('Tiêu đề công việc')->required()->maxLength(255)->columnSpanFull(),
                    Select::make('employer_id')->label('Doanh nghiệp')->relationship('employer', 'name')->searchable()->preload()->required()
                        ->columnSpan(['default' => 1, 'xl' => 2]),
                    Select::make('industry')->label('Lĩnh vực nghề nghiệp')->options(fn (): array => CareerField::options(onlyActive: true))->searchable()->required(),
                    TextInput::make('recruitment_count')->label('Số người cần')->numeric()->minValue(1)->maxValue(100000)->required(),
                    DatePicker::make('deadline')->label('Hạn nộp')->displayFormat('d/m/Y'),
                    Select::make('status')->label('Trạng thái')->options(['DRAFT' => 'Bản nháp', 'PUBLISHED' => 'Đã công bố', 'CLOSED' => 'Đã đóng'])->default('DRAFT')->required(),
                ]),
            Section::make('Địa điểm và điều kiện làm việc')
                ->columnSpanFull()
                ->columns(['default' => 1, 'md' => 2, 'xl' => 3])
                ->schema([
                    TextInput::make('details.address')->label('Địa chỉ làm việc')->maxLength(1000)->columnSpan(['default' => 1, 'xl' => 2]),
                    TextInput::make('details.ward_name')->label('Xã / Phường')->maxLength(1000),
                    TextInput::make('details.salary')->label('Mức lương')->maxLength(1000),
                    TextInput::make('details.education')->label('Trình độ')->maxLength(1000),
                    TextInput::make('details.experience')->label('Kinh nghiệm')->maxLength(1000),
                ]),
            Section::make('Nội dung tuyển dụng')
                ->columnSpanFull()
                ->columns(['default' => 1, 'lg' => 2])
                ->schema($contentFields),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->modifyQueryUsing(fn ($query) => $query->with('careerField'))->columns([
            TextColumn::make('title')->label('Công việc')->searchable()->wrap(),
            TextColumn::make('employer.name')->label('Doanh nghiệp')->searchable()->wrap(),
            TextColumn::make('industry')->label('Lĩnh vực')->formatStateUsing(fn (?string $state, JobPosting $record): string => $record->careerField?->name ?? ($state ? (JobPostingExcelService::FIELDS[$state] ?? $state) : ''))->wrap(),
            TextColumn::make('recruitment_count')->label('Số người')->numeric(),
            TextColumn::make('deadline')->label('Hạn nộp')->date('d/m/Y'),
            TextColumn::make('status')->label('Trạng thái')->badge()->formatStateUsing(fn ($state) => ['DRAFT' => 'Nháp', 'PUBLISHED' => 'Công bố', 'CLOSED' => 'Đóng'][$state] ?? $state),
        ])->recordActions([EditAction::make()])->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make()->label('Xóa các mục đã chọn'),
            ]),
        ])->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListJobPostings::route('/'), 'create' => Pages\CreateJobPosting::route('/create'), 'edit' => Pages\EditJobPosting::route('/{record}/edit')];
    }
}
