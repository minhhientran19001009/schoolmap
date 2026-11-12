<?php

namespace Tests\Feature;

use App\Filament\Resources\Skills\Pages\EditSkill;
use App\Filament\Resources\Skills\Pages\ListSkills;
use App\Filament\Resources\Skills\SkillResource;
use App\Models\Skill;
use App\Services\JobPostingExcelService;
use Database\Seeders\SkillCatalogSeeder;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema as DbSchema;
use Tests\TestCase;

class SkillCatalogTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');

        foreach (['skills', 'training_majors', 'training_levels', 'reporting_periods'] as $table) {
            DbSchema::dropIfExists($table);
        }

        DbSchema::create('skills', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('normalized_name', 160)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DbSchema::create('training_majors', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
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
        });
    }

    public function test_migration_and_seeder_populate_all_career_fields_idempotently(): void
    {
        $existing = Skill::query()->create(['name' => 'PLC', 'is_active' => false]);

        $migration = require database_path('migrations/2026_11_12_000002_add_career_field_to_skills_table.php');
        $migration->up();

        $expectedCount = collect(SkillCatalogSeeder::CATALOG)->flatten()->count();
        $this->assertSame(100, $expectedCount);
        $this->assertDatabaseCount('skills', $expectedCount);

        foreach (array_keys(Skill::CAREER_FIELDS) as $careerField) {
            $this->assertTrue(
                Skill::query()->where('career_field', $careerField)->exists(),
                "Missing seeded skills for career field: {$careerField}",
            );
        }

        $existing->refresh();
        $this->assertSame('dien_dien_tu', $existing->career_field);
        $this->assertFalse($existing->is_active);

        $first = Skill::query()->where('normalized_name', Skill::normalize('Đọc bản vẽ'))->firstOrFail();
        $first->update(['career_field' => 'dien_dien_tu']);

        (new SkillCatalogSeeder)->run();
        $this->assertDatabaseCount('skills', $expectedCount);
        $this->assertSame('dien_dien_tu', $first->fresh()->career_field);
        $this->assertSame(Skill::CAREER_FIELDS, JobPostingExcelService::FIELDS);
    }

    public function test_legacy_skill_catalog_is_disabled_and_public_catalog_only_exposes_career_fields(): void
    {
        (require database_path('migrations/2026_11_12_000002_add_career_field_to_skills_table.php'))->up();

        $form = SkillResource::form(Schema::make(app(EditSkill::class)));
        $fieldNames = collect($form->getFlatComponents(withHidden: true))
            ->map(fn ($component) => method_exists($component, 'getName') ? $component->getName() : null)
            ->filter()
            ->all();
        $this->assertContains('career_field', $fieldNames);

        $table = SkillResource::table(Table::make(app(ListSkills::class)));
        $this->assertArrayHasKey('career_field', $table->getColumns());
        $this->assertArrayHasKey('career_field', $table->getFilters());

        Skill::query()->where('normalized_name', Skill::normalize('PLC'))->update(['is_active' => false]);

        $response = $this->getJson('/api/supply-demand/catalogs')->assertOk();
        $response->assertJsonCount(13, 'career_fields');
        $response->assertJsonMissingPath('skills');
        $this->assertFalse(SkillResource::canAccess());
        $this->assertFalse(SkillResource::shouldRegisterNavigation());
    }
}
