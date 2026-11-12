<?php

namespace Tests\Feature;

use App\Models\TrainingCapacity;
use App\Models\WorkforceDemand;
use App\Support\SkillList;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SkillStorageMigrationTest extends TestCase
{
    public function test_migration_preserves_legacy_skills_and_rollback_keeps_new_skills(): void
    {
        Schema::create('skills', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('normalized_name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        foreach (['workforce_demands', 'training_capacities'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table): void {
                $table->id();
                $table->string('salary')->nullable();
                $table->string('equipment')->nullable();
                $table->timestamps();
            });
            DB::table($tableName)->insert(['id' => 1]);
        }
        foreach (['demand_skill' => 'workforce_demand_id', 'capacity_skill' => 'training_capacity_id'] as $tableName => $key) {
            Schema::create($tableName, function (Blueprint $table) use ($key): void {
                $table->unsignedBigInteger($key);
                $table->unsignedBigInteger('skill_id');
                $table->primary([$key, 'skill_id']);
            });
            DB::table($tableName)->insert([$key => 1, 'skill_id' => 1]);
        }
        DB::table('skills')->insert(['id' => 1, 'name' => 'Đọc bản vẽ', 'normalized_name' => 'doc ban ve']);

        $migration = require database_path('migrations/2026_11_12_000004_store_skills_on_supply_demand_records.php');
        $migration->up();
        $this->assertSame(['Đọc bản vẽ'], WorkforceDemand::findOrFail(1)->skills);
        $this->assertSame(['Đọc bản vẽ'], TrainingCapacity::findOrFail(1)->skills);
        WorkforceDemand::findOrFail(1)->update(['skills' => ['Hàn TIG']]);
        $migration->up();
        $this->assertSame(['Hàn TIG'], WorkforceDemand::findOrFail(1)->skills);
        $migration->down();
        $this->assertFalse(Schema::hasColumn('workforce_demands', 'skills'));
        $this->assertDatabaseHas('skills', ['name' => 'Hàn TIG']);
        $newId = DB::table('skills')->where('name', 'Hàn TIG')->value('id');
        $this->assertDatabaseHas('demand_skill', ['workforce_demand_id' => 1, 'skill_id' => $newId]);
        $this->assertDatabaseMissing('demand_skill', ['workforce_demand_id' => 1, 'skill_id' => 1]);
    }

    public function test_free_text_supports_commas_newlines_and_vietnamese_duplicates(): void
    {
        $this->assertSame(['Đọc bản vẽ', 'Hàn TIG', 'PLC'], SkillList::parse(" Đọc bản vẽ, Hàn TIG\r\nPLC; doc ban ve, ,"));
    }
}
