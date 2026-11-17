<?php

namespace Tests\Feature;

use App\Models\SchoolSourceRecord;
use App\Services\TrainingScaleExcelService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class TrainingScaleExcelTest extends TestCase
{
    private array $files = [];

    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('schools', function (Blueprint $table): void {
            $table->string('id', 50)->primary();
            $table->string('name');
            $table->string('code');
            $table->integer('student_count')->default(111);
        });
        (require database_path('migrations/2026_11_12_000001_create_school_source_records_table.php'))->up();
        DB::table('schools')->insert(['id' => 'different-local-id', 'name' => 'Trường kiểm thử', 'code' => 'VPS-CODE']);
    }

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            File::delete($file);
        }
        parent::tearDown();
    }

    private function workbook(array $values = [100, 20, 80, 0]): string
    {
        $book = new Spreadsheet;
        $main = $book->getActiveSheet()->setTitle('Mau_Nhap_Lieu');
        $main->setCellValue('A4', 'Trường kiểm thử');
        $main->setCellValue('B4', 'LOCAL-CODE');
        $summary = $book->createSheet()->setTitle('QM_Tong_hop');
        $summary->fromArray([['Tên cơ sở trong nguồn', 'Tên cơ sở trong mẫu nhập', 'Dòng mẫu nhập', 'Mã nhóm', 'Dòng nguồn', 'Tổng quy mô nguồn']]);
        $summary->fromArray([['Trường kiểm thử', 'Trường kiểm thử', 4, 'LOCAL-CODE', 10, ...$values]], null, 'A2', true);
        $detail = $book->createSheet()->setTitle('QM_Theo_nganh');
        $detail->fromArray([['Tên cơ sở trong nguồn', 'Tên cơ sở trong mẫu nhập', 'Dòng mẫu nhập', 'Tên ngành chuẩn hóa', 'Tên gốc', 'Dòng nguồn', 'Tổng quy mô nguồn (gộp)', 'Cao đẳng nguồn (gộp)', 'Trung cấp nguồn (gộp)', 'Sơ cấp và khác nguồn (gộp)']]);
        $detail->fromArray([['Trường kiểm thử', 'Trường kiểm thử', 4, 'Hàn', 'Hàn', '6, 7', ...$values]], null, 'A2', true);
        $path = tempnam(sys_get_temp_dir(), 'school-scale-');
        (new Xlsx($book))->save($path);
        $this->files[] = $path;

        return $path;
    }

    public function test_preview_apply_and_repeat_preserve_other_school_data_codes_and_planned_figures(): void
    {
        SchoolSourceRecord::create([
            'source_key' => 'existing', 'record_type' => 'training_report', 'school_id' => 'different-local-id',
            'source_name' => 'Trường kiểm thử', 'match_status' => 'matched', 'content_hash' => str_repeat('a', 64),
            'payload' => ['programs' => [['name' => 'Hàn', 'source_codes' => ['6520123'], 'planned_2026' => ['clean_total' => 555]]]],
        ]);
        $path = $this->workbook();
        $service = app(TrainingScaleExcelService::class);
        $preview = $service->importFromFile($path);
        $this->assertTrue($preview['success'], implode('; ', $preview['errors']));
        $this->assertSame(1, $preview['updated']);
        $this->assertArrayNotHasKey('current_scale', SchoolSourceRecord::first()->payload['programs'][0]);
        $result = $service->importFromFile($path, false);
        $this->assertTrue($result['success'], implode('; ', $result['errors']));
        $this->files[] = $result['backup'];
        $payload = SchoolSourceRecord::first()->payload;
        $this->assertSame(100, $payload['programs'][0]['current_scale']['clean_total']);
        $this->assertSame(['6520123'], $payload['programs'][0]['source_codes']);
        $this->assertSame(555, $payload['programs'][0]['planned_2026']['clean_total']);
        $this->assertSame([6, 7], $payload['programs'][0]['source_rows']);
        $this->assertDatabaseHas('schools', ['id' => 'different-local-id', 'code' => 'VPS-CODE', 'student_count' => 111]);
        $repeat = $service->importFromFile($path, false);
        $this->assertSame(1, $repeat['unchanged']);
        $this->assertSame(0, $repeat['updated']);
        $this->assertDatabaseCount('school_source_records', 1);
    }

    public function test_invalid_later_row_does_not_partially_import(): void
    {
        $path = $this->workbook();
        $book = IOFactory::load($path);
        $book->getSheetByName('QM_Theo_nganh')->fromArray([['Trường kiểm thử', 'Trường kiểm thử', 4, 'Điện', 'Điện', '8', -5, 0, 0, 0]], null, 'A3', true);
        (new Xlsx($book))->save($path);
        $result = app(TrainingScaleExcelService::class)->importFromFile($path, false);
        $this->assertFalse($result['success']);
        $this->assertDatabaseCount('school_source_records', 0);
    }

    public function test_blank_and_conflicting_totals_preserve_source_values(): void
    {
        $service = app(TrainingScaleExcelService::class);
        $result = $service->importFromFile($this->workbook([0, null, 450, 4700]), false);
        $this->assertTrue($result['success'], implode('; ', $result['errors']));
        $this->files[] = $result['backup'];
        $scale = SchoolSourceRecord::first()->payload['programs'][0]['current_scale'];
        $this->assertSame(0, $scale['reported_total']);
        $this->assertNull($scale['clean_total']);
        $this->assertNull($scale['cao_dang']);
        $this->assertSame(4700, $scale['so_cap_va_khac']);
        $this->assertNotEmpty($scale['issues']);
    }

    public function test_ambiguous_school_names_are_not_selected(): void
    {
        DB::table('schools')->insert(['id' => 'another', 'name' => 'Trường kiểm thử', 'code' => 'OTHER']);
        $result = app(TrainingScaleExcelService::class)->importFromFile($this->workbook(), false);
        $this->assertFalse($result['success']);
        $this->assertDatabaseCount('school_source_records', 0);
    }

    public function test_demo_provenance_and_optional_levels_are_preserved_without_touching_school_counts(): void
    {
        $path = $this->workbook([100, 0, 0, 0]);
        $book = IOFactory::load($path);
        foreach (['QM_Tong_hop', 'QM_Theo_nganh'] as $title) {
            $sheet = $book->getSheetByName($title);
            $sheet->setCellValue('O1', 'Đại học');
            $sheet->setCellValue('O2', 100);
            $sheet->setCellValue('P1', 'Trường số giả lập');
            $sheet->setCellValue('P2', 'reported_total, dai_hoc');
        }
        (new Xlsx($book))->save($path);
        $result = app(TrainingScaleExcelService::class)->importFromFile($path, false);
        $this->assertTrue($result['success'], implode('; ', $result['errors']));
        $this->files[] = $result['backup'];
        $payload = SchoolSourceRecord::first()->payload;
        $this->assertSame(100, $payload['programs'][0]['current_scale']['dai_hoc']);
        $this->assertSame(100, $payload['programs'][0]['current_scale']['clean_total']);
        $this->assertSame(['reported_total', 'dai_hoc'], $payload['programs'][0]['demo_fields']);
        $this->assertTrue($payload['source_metadata']['contains_demo_figures']);
        $this->assertDatabaseHas('schools', ['id' => 'different-local-id', 'student_count' => 111]);
    }

    public function test_prepared_workbook_has_fifteen_schools_and_256_programs(): void
    {
        $path = base_path('../docs/exports/Danh_Sach_Truong_Hoc_Ninh_Binh_2026_11_17_Nganh_Va_Quy_Mo.xlsx');
        $book = IOFactory::load($path);
        $summary = $book->getSheetByName('QM_Tong_hop');
        for ($row = 2; $row <= $summary->getHighestDataRow(); $row++) {
            DB::table('schools')->insert(['id' => 'fixture-'.$row, 'name' => $summary->getCell('B'.$row)->getValue(), 'code' => 'SERVER-'.$row]);
        }
        $result = app(TrainingScaleExcelService::class)->importFromFile($path, false);
        $this->assertTrue($result['success'], implode('; ', $result['errors']));
        $this->files[] = $result['backup'];
        $this->assertSame(15, $result['created']);
        $this->assertSame(256, $result['programs']);
        $this->assertDatabaseCount('school_source_records', 15);
    }
}
