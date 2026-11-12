<?php

namespace Tests\Feature;

use App\Filament\Resources\Employers\EmployerResource;
use App\Filament\Resources\Employers\Pages\EditEmployer;
use App\Filament\Resources\Employers\Pages\ListEmployers;
use App\Filament\Resources\JobPostings\Pages\ListJobPostings;
use App\Filament\Resources\WorkforceDemands\Pages\ListWorkforceDemands;
use App\Models\Employer;
use App\Models\JobPosting;
use App\Models\ReportingPeriod;
use App\Models\TrainingLevel;
use App\Models\TrainingMajor;
use App\Services\EmployerExcelService;
use App\Services\JobPostingExcelService;
use App\Services\WorkforceDemandExcelService;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema as DbSchema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ReflectionMethod;
use Tests\TestCase;

class ExcelCatalogImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        foreach (['job_postings', 'demand_skill', 'workforce_demands', 'skills', 'reporting_periods', 'training_levels', 'training_majors', 'employers', 'wards'] as $table) {
            DbSchema::dropIfExists($table);
        }
        DbSchema::create('wards', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('full_name')->nullable();
        });
        DbSchema::create('employers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->json('aliases')->nullable();
            $table->string('tax_code')->nullable()->unique();
            $table->string('industry')->nullable();
            $table->string('region')->nullable();
            $table->unsignedInteger('employee_count')->nullable();
            $table->string('source_code')->nullable();
            $table->string('ward_id')->nullable();
            $table->string('address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('scope')->nullable();
            $table->text('data_note')->nullable();
            $table->json('source_details')->nullable();
            $table->date('reviewed_at')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->boolean('is_verified')->default(false);
            $table->string('source')->default('SYSTEM');
            $table->string('external_id')->nullable();
            $table->timestamps();
        });
        DbSchema::create('training_majors', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        DbSchema::create('training_levels', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->string('name');
            $table->unsignedSmallInteger('display_order')->default(1);
            $table->boolean('is_active')->default(true);
        });
        DbSchema::create('reporting_periods', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->date('starts_at');
            $table->date('ends_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        DbSchema::create('skills', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('normalized_name')->unique();
            $table->string('career_field', 60)->nullable()->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        DbSchema::create('workforce_demands', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('employer_id');
            $table->unsignedBigInteger('training_major_id');
            $table->string('education_level_id');
            $table->unsignedBigInteger('reporting_period_id');
            $table->string('purpose')->default('RECRUITMENT');
            $table->unsignedInteger('quantity');
            $table->string('location')->nullable();
            $table->string('age_range')->nullable();
            $table->string('salary')->nullable();
            $table->json('skills')->nullable();
            $table->text('note')->nullable();
            $table->string('status')->default('DRAFT');
            $table->string('source')->default('SYSTEM');
            $table->string('external_id')->nullable()->unique();
            $table->boolean('is_sample')->default(false);
            $table->timestamps();
        });
    }

    public function test_import_modals_use_the_expected_forms(): void
    {
        foreach ([[ListEmployers::class, 'employer-excel-import-form'], [ListWorkforceDemands::class, 'workforce-demand-excel-import-form']] as [$pageClass, $formId]) {
            $page = app($pageClass);
            $method = new ReflectionMethod($page, 'getHeaderActions');
            $method->setAccessible(true);
            /** @var Action $action */
            $action = collect($method->invoke($page))->first(fn (Action $item) => $item->getName() === 'importExcel');
            $this->assertFalse($action->hasFormWrapper());
            $this->assertStringContainsString('id="'.$formId.'"', $action->getModalContent()->render());
            $this->assertSame($formId, $action->getModalSubmitAction()->getFormToSubmit());
        }
    }

    public function test_templates_and_exports_can_be_generated(): void
    {
        $employerService = app(EmployerExcelService::class);
        $demandService = app(WorkforceDemandExcelService::class);

        $this->assertStringContainsString('khung_nhap_doanh_nghiep.xlsx', $employerService->downloadTemplate()->headers->get('content-disposition'));
        $this->assertStringContainsString('danh_sach_doanh_nghiep.xlsx', $employerService->exportCurrentEmployers()->headers->get('content-disposition'));
        $this->assertStringContainsString('khung_nhap_nhu_cau_nhan_luc.xlsx', $demandService->downloadTemplate()->headers->get('content-disposition'));
        $this->assertStringContainsString('danh_sach_nhu_cau_nhan_luc.xlsx', $demandService->exportCurrentDemands()->headers->get('content-disposition'));
    }

    public function test_employer_import_supports_dry_run_and_atomic_validation(): void
    {
        $path = $this->xlsx([
            ['Tên doanh nghiệp', 'Mã số thuế', 'Lĩnh vực hoạt động', 'Trạng thái'],
            ['Công ty kiểm thử Excel', 'TEST-001', 'Cơ khí', 'Hoạt động'],
        ]);
        $service = app(EmployerExcelService::class);
        $dryRun = $service->importFromFile($path, true);
        $this->assertSame(1, $dryRun['created']);
        $this->assertFalse($dryRun['committed']);
        $this->assertDatabaseMissing('employers', ['tax_code' => 'TEST-001']);
        $applied = $service->importFromFile($path);
        $this->assertTrue($applied['committed']);
        $this->assertDatabaseHas('employers', ['tax_code' => 'TEST-001', 'industry' => 'Cơ khí']);
        @unlink($path);

        $invalid = $this->xlsx([
            ['Tên doanh nghiệp', 'Mã số thuế', 'Email'],
            ['Công ty hợp lệ nhưng không được ghi', 'TEST-002', 'valid@example.test'],
            ['Công ty lỗi', 'TEST-003', 'email-khong-hop-le'],
        ]);
        $result = $service->importFromFile($invalid);
        $this->assertNotEmpty($result['errors']);
        $this->assertFalse($result['committed']);
        $this->assertDatabaseMissing('employers', ['tax_code' => 'TEST-002']);
        @unlink($invalid);
    }

    public function test_workforce_demand_import_resolves_catalogs_and_skills(): void
    {
        $employer = Employer::query()->create(['name' => 'Doanh nghiệp Excel', 'tax_code' => 'DN-001']);
        $major = TrainingMajor::query()->create(['name' => 'Công nghệ ô tô', 'code' => 'AUTO-01']);
        TrainingLevel::query()->create(['id' => 'cao_dang', 'name' => 'Cao đẳng', 'display_order' => 1]);
        ReportingPeriod::query()->create(['code' => '2027-Q1', 'name' => 'Quý I/2027', 'starts_at' => '2027-01-01', 'ends_at' => '2027-03-31']);
        $path = $this->xlsx([
            ['Mã nhu cầu', 'Tên doanh nghiệp', 'Mã số thuế doanh nghiệp', 'Kỳ báo cáo', 'Mã ngành / nghề', 'Ngành / nghề', 'Trình độ', 'Số người cần', 'Kỹ năng yêu cầu', 'Trạng thái'],
            ['JOB-001', $employer->name, $employer->tax_code, '2027-Q1', $major->code, $major->name, 'Cao đẳng', '25', 'Đọc bản vẽ, An toàn 5S', 'Đã công bố'],
        ]);
        $service = app(WorkforceDemandExcelService::class);
        $dryRun = $service->importFromFile($path, true);
        $this->assertSame(1, $dryRun['created']);
        $this->assertDatabaseCount('workforce_demands', 0);
        $result = $service->importFromFile($path);
        $this->assertTrue($result['committed']);
        $this->assertDatabaseHas('workforce_demands', ['external_id' => 'JOB-001', 'quantity' => 25, 'status' => 'PUBLISHED']);
        $this->assertSame(
            ['Đọc bản vẽ', 'An toàn 5S'],
            \App\Models\WorkforceDemand::query()->where('external_id', 'JOB-001')->firstOrFail()->skills,
        );
        @unlink($path);
    }

    public function test_legacy_directory_migration_is_repeatable_and_public_api_respects_status(): void
    {
        (require database_path('migrations/2026_11_12_000000_create_job_postings_table.php'))->up();
        $this->artisan('directory:import-legacy', ['--dry-run' => true])->assertSuccessful();
        $this->assertDatabaseCount('job_postings', 0);
        $this->artisan('directory:import-legacy')->assertSuccessful();
        $this->assertDatabaseCount('job_postings', 80);
        $this->assertDatabaseCount('employers', 94);
        $job = JobPosting::first();
        $job->update(['title' => 'Tiêu đề quản trị đã chỉnh sửa']);
        $this->artisan('directory:import-legacy')->assertSuccessful();
        $this->assertDatabaseCount('job_postings', 80);
        $this->assertDatabaseCount('employers', 94);
        $this->assertSame('Tiêu đề quản trị đã chỉnh sửa', $job->fresh()->title);
        $this->getJson('/api/public/directory')->assertOk()->assertJsonCount(80, 'jobs')->assertJsonCount(94, 'employers');
        $job->update(['status' => 'CLOSED']);
        $this->getJson('/api/public/directory')->assertOk()->assertJsonCount(79, 'jobs');
        $this->get('/admin/job-postings')->assertRedirect('/admin/login');
    }

    public function test_job_excel_import_preserves_existing_optional_fields_and_rejects_bad_dates(): void
    {
        (require database_path('migrations/2026_11_12_000000_create_job_postings_table.php'))->up();
        Employer::create(['name' => 'Doanh nghiệp Excel', 'tax_code' => 'DN-001']);
        $headers = ['Mã tin (*)', 'Mã số thuế doanh nghiệp', 'Tên doanh nghiệp (*)', 'Tiêu đề công việc (*)', 'Lĩnh vực (*)', 'Số người cần (*)', 'Hạn nộp (dd/mm/yyyy)', 'Địa chỉ làm việc', 'Xã / Phường', 'Mức lương', 'Trình độ', 'Kinh nghiệm', 'Mô tả công việc', 'Yêu cầu', 'Quyền lợi', 'Kỹ năng', 'Trạng thái'];
        $row = ['TEST-JOB', 'DN-001', 'Doanh nghiệp Excel', 'Thợ cơ khí', 'co_khi', '12', '31/12/2027', 'Ninh Bình', '', '10 triệu', 'Cao đẳng', '', 'Lắp ráp', 'Đọc bản vẽ', '', 'Hàn; Lắp ráp', 'PUBLISHED'];
        $path = $this->xlsx([$headers, $row]);
        $service = app(JobPostingExcelService::class);
        $this->assertSame([], $service->importFromFile($path, true)['errors']);
        $this->assertDatabaseCount('job_postings', 0);
        $this->assertTrue($service->importFromFile($path)['committed']);
        $this->assertDatabaseCount('job_postings', 1);
        @unlink($path);
        $row[3] = 'Thợ cơ khí cập nhật';
        $row[9] = '';
        $row[16] = '';
        $path = $this->xlsx([$headers, $row]);
        $this->assertTrue($service->importFromFile($path)['committed']);
        $this->assertSame('10 triệu', JobPosting::find('TEST-JOB')->details['salary']);
        $this->assertSame('PUBLISHED', JobPosting::find('TEST-JOB')->status);
        @unlink($path);
        $valid = $row;
        $valid[0] = 'TEST-VALID';
        $row[0] = 'TEST-INVALID';
        $row[6] = '31/02/2027';
        $path = $this->xlsx([$headers, $valid, $row]);
        $this->assertNotEmpty($service->importFromFile($path)['errors']);
        $this->assertDatabaseCount('job_postings', 1);
        @unlink($path);

        $this->assertStringContainsString('khung_nhap_tin_viec_lam.xlsx', $service->downloadTemplate()->headers->get('content-disposition'));
        $page = app(ListJobPostings::class);
        $method = new ReflectionMethod($page, 'getHeaderActions');
        $method->setAccessible(true);
        $action = collect($method->invoke($page))->first(fn ($item) => $item->getName() === 'importExcel');
        $this->assertStringContainsString('job-posting-excel-import-form', $action->getModalContent()->render());
    }

    public function test_job_id_is_generated_when_creating_without_a_code_and_stays_stable(): void
    {
        (require database_path('migrations/2026_11_12_000000_create_job_postings_table.php'))->up();
        $employer = Employer::create(['name' => 'Doanh nghiệp kiểm thử']);
        $job = JobPosting::create([
            'employer_id' => $employer->id, 'title' => 'Thợ cơ khí', 'industry' => 'co_khi',
            'recruitment_count' => 5, 'details' => [], 'status' => 'DRAFT',
        ]);
        $this->assertStringStartsWith('job-', $job->id);
        $originalId = $job->id;
        $job->update(['title' => 'Thợ cơ khí cập nhật']);
        $this->assertSame($originalId, $job->fresh()->id);
        $legacy = JobPosting::create([
            'id' => 'source-job-existing', 'employer_id' => $employer->id, 'title' => 'Tin cũ',
            'recruitment_count' => 1, 'details' => [],
        ]);
        $this->assertSame('source-job-existing', $legacy->fresh()->id);
    }

    public function test_admin_employer_resource_and_excel_omit_verification(): void
    {
        $table = EmployerResource::table(Table::make(app(ListEmployers::class)));
        $this->assertArrayNotHasKey('is_verified', $table->getColumns());

        $form = EmployerResource::form(Schema::make(app(EditEmployer::class)));
        $fieldNames = collect($form->getFlatComponents(withHidden: true))
            ->map(fn ($component) => method_exists($component, 'getName') ? $component->getName() : null)
            ->filter()
            ->all();
        $this->assertNotContains('is_verified', $fieldNames);

        $service = app(EmployerExcelService::class);
        foreach ([$service->downloadTemplate(), $service->exportCurrentEmployers()] as $index => $response) {
            ob_start();
            $response->sendContent();
            $content = ob_get_clean();
            $path = tempnam(sys_get_temp_dir(), 'schoolmap-xlsx-check-');
            file_put_contents($path, $content);
            $spreadsheet = IOFactory::load($path);
            $headerRow = $index === 0 ? 4 : 1;
            $headers = $spreadsheet->getActiveSheet()->rangeToArray("A{$headerRow}:N{$headerRow}")[0];
            $this->assertNotContains('Đã xác minh', $headers);
            if ($index === 0) {
                $catalogHeaders = $spreadsheet->getSheetByName('Danh_Muc')->rangeToArray('A1:C1')[0];
                $this->assertNotContains('Xác minh', $catalogHeaders);
            }
            @unlink($path);
        }
    }

    private function xlsx(array $rows): string
    {
        $path = tempnam(sys_get_temp_dir(), 'schoolmap-excel-');
        $spreadsheet = new Spreadsheet;
        $spreadsheet->getActiveSheet()->fromArray($rows);
        (new Xlsx($spreadsheet))->save($path);

        return $path;
    }
}
