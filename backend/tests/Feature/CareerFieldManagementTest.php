<?php

namespace Tests\Feature;

use App\Filament\Resources\CareerFields\CareerFieldResource;
use App\Filament\Resources\CareerFields\Pages\CreateCareerField;
use App\Filament\Resources\CareerFields\Pages\EditCareerField;
use App\Filament\Resources\CareerFields\Pages\ListCareerFields;
use App\Models\CareerField;
use App\Models\Skill;
use Database\Seeders\CareerFieldSeeder;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema as DbSchema;
use Tests\TestCase;

class CareerFieldManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');

        foreach (['job_postings', 'skills', 'career_fields', 'employers', 'training_majors', 'training_levels', 'reporting_periods'] as $table) {
            DbSchema::dropIfExists($table);
        }

        DbSchema::create('employers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('tax_code')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->string('public_id')->nullable();
            $table->json('public_profile')->nullable();
            $table->string('industry')->nullable();
            $table->string('region')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->unsignedInteger('employee_count')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });

        DbSchema::create('job_postings', function (Blueprint $table): void {
            $table->string('id', 100)->primary();
            $table->unsignedBigInteger('employer_id');
            $table->string('title');
            $table->string('industry', 60)->nullable();
            $table->unsignedInteger('recruitment_count')->default(1);
            $table->date('deadline')->nullable();
            $table->string('status', 20)->default('PUBLISHED');
            $table->json('details');
            $table->timestamps();
        });

        DbSchema::create('skills', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('normalized_name', 160)->unique();
            $table->string('career_field', 60)->nullable();
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

    public function test_migration_and_seeder_populate_career_fields_idempotently_and_preserve_edits(): void
    {
        (require database_path('migrations/2026_11_12_000003_create_career_fields_table.php'))->up();

        $this->assertDatabaseCount('career_fields', 13);

        $field = CareerField::query()->findOrFail('cntt');
        $field->update([
            'name' => 'Công nghệ thông tin & Chuyển đổi số',
            'is_active' => false,
        ]);

        (new CareerFieldSeeder)->run();

        $this->assertDatabaseCount('career_fields', 13);
        $field->refresh();
        $this->assertSame('Công nghệ thông tin & Chuyển đổi số', $field->name);
        $this->assertFalse($field->is_active);
    }

    public function test_custom_career_field_propagates_to_options_resource_and_public_apis(): void
    {
        (require database_path('migrations/2026_11_12_000003_create_career_fields_table.php'))->up();

        $created = CareerField::query()->create([
            'name' => 'Năng lượng tái tạo',
            'icon' => 'fa-solid fa-solar-panel',
        ]);

        $this->assertSame('nang_luong_tai_tao', $created->id);
        $this->assertSame(14, $created->display_order);
        $this->assertArrayHasKey('nang_luong_tai_tao', CareerField::options(onlyActive: true));

        $skill = Skill::query()->create([
            'name' => 'Lắp đặt điện mặt trời',
            'career_field' => 'nang_luong_tai_tao',
        ]);
        $this->assertSame('Năng lượng tái tạo', $skill->careerFieldLabel());

        CareerField::query()->whereKey('y_te')->update(['is_active' => false]);
        $this->assertArrayNotHasKey('y_te', CareerField::options(onlyActive: true));
        $this->assertArrayHasKey('y_te', CareerField::options(onlyActive: false));

        $createForm = CareerFieldResource::form(Schema::make(app(CreateCareerField::class)));
        $createFields = collect($createForm->getFlatComponents(withHidden: true))
            ->map(fn ($c) => method_exists($c, 'getName') ? $c->getName() : null)
            ->filter()
            ->values()
            ->all();
        $this->assertSame(['name'], $createFields);

        $editForm = CareerFieldResource::form(Schema::make(app(EditCareerField::class))->record($created));
        $editFields = collect($editForm->getFlatComponents(withHidden: true))
            ->map(fn ($c) => method_exists($c, 'getName') ? $c->getName() : null)
            ->filter()
            ->values()
            ->all();
        $this->assertSame(['name'], $editFields);

        $table = CareerFieldResource::table(Table::make(app(ListCareerFields::class)));
        $this->assertSame(['name'], array_keys($table->getColumns()));
        $this->assertSame([], $table->getFilters());

        $this->getJson('/api/supply-demand/catalogs')
            ->assertOk()
            ->assertJsonCount(13, 'career_fields')
            ->assertJsonFragment(['id' => 'nang_luong_tai_tao', 'name' => 'Năng lượng tái tạo']);

        $this->getJson('/api/public/directory')
            ->assertOk()
            ->assertJsonCount(13, 'career_fields')
            ->assertJsonFragment(['id' => 'nang_luong_tai_tao', 'icon' => 'fa-solid fa-solar-panel']);
    }
}
