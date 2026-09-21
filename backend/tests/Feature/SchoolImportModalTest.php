<?php

namespace Tests\Feature;

use App\Filament\Resources\Schools\Pages\ListSchools;
use Filament\Actions\Action;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use ReflectionMethod;
use Tests\TestCase;

class SchoolImportModalTest extends TestCase
{
    public function test_excel_import_modal_uses_valid_filament_markup(): void
    {
        $page = app(ListSchools::class);
        $getHeaderActions = new ReflectionMethod($page, 'getHeaderActions');
        $getHeaderActions->setAccessible(true);

        /** @var Action $action */
        $action = collect($getHeaderActions->invoke($page))
            ->first(fn (Action $action): bool => $action->getName() === 'importExcel');

        $this->assertFalse($action->hasFormWrapper());
        $this->assertSame(Width::Large, $action->getModalWidth());
        $this->assertSame(Alignment::End, $action->getModalFooterActionsAlignment());

        $content = $action->getModalContent()->render();

        $this->assertStringContainsString('id="school-excel-import-form"', $content);
        $this->assertStringContainsString('class="fi-input-wrp"', $content);
        $this->assertStringContainsString('class="fi-input"', $content);

        $submitAction = $action->getModalSubmitAction();

        $this->assertTrue($submitAction->canSubmitForm());
        $this->assertSame('school-excel-import-form', $submitAction->getFormToSubmit());
        $this->assertSame('school-excel-import-form', $submitAction->getFormId());
    }
}
