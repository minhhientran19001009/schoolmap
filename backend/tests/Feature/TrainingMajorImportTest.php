<?php

namespace Tests\Feature;

use App\Filament\Resources\TrainingMajors\Pages\ListTrainingMajors;
use App\Services\TrainingMajorExcelService;
use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ReflectionMethod;
use Tests\TestCase;

class TrainingMajorImportTest extends TestCase
{
    public function test_training_major_import_modal_uses_valid_filament_markup(): void
    {
        $page = app(ListTrainingMajors::class);
        $getHeaderActions = new ReflectionMethod($page, 'getHeaderActions');
        $getHeaderActions->setAccessible(true);

        /** @var Action $action */
        $action = collect($getHeaderActions->invoke($page))
            ->first(fn (Action $action): bool => $action->getName() === 'importExcel');

        $this->assertFalse($action->hasFormWrapper());
        $this->assertSame(Width::Large, $action->getModalWidth());
        $this->assertSame(Alignment::End, $action->getModalFooterActionsAlignment());

        $content = $action->getModalContent()->render();

        $this->assertStringContainsString('id="training-major-excel-import-form"', $content);
        $this->assertStringContainsString('class="fi-input-wrp"', $content);
        $this->assertStringContainsString('class="fi-input"', $content);

        $submitAction = $action->getModalSubmitAction();

        $this->assertTrue($submitAction->canSubmitForm());
        $this->assertSame('training-major-excel-import-form', $submitAction->getFormToSubmit());
        $this->assertSame('training-major-excel-import-form', $submitAction->getFormId());
    }

    public function test_training_major_excel_import_creates_valid_rows_and_reports_duplicates(): void
    {
        $filePath = tempnam(sys_get_temp_dir(), 'training-major-');
        $majorName = 'Ngành kiểm thử Excel '.uniqid();
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray([
            ['Tên chuyên ngành / nghề đào tạo', 'Đang sử dụng'],
            [$majorName, 'Có'],
            [$majorName, 'Không'],
        ]);
        (new Xlsx($spreadsheet))->save($filePath);

        DB::beginTransaction();
        try {
            $result = app(TrainingMajorExcelService::class)->importFromFile($filePath);

            $this->assertTrue($result['success']);
            $this->assertSame(2, $result['total']);
            $this->assertSame(1, $result['created']);
            $this->assertSame(0, $result['updated']);
            $this->assertCount(1, $result['errors']);
        } finally {
            DB::rollBack();
            @unlink($filePath);
        }
    }
}
