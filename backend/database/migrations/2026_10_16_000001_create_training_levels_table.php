<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_levels', function (Blueprint $table): void {
            $table->string('id', 20)->primary();
            $table->string('name', 50)->unique();
            $table->unsignedSmallInteger('display_order')->default(1);
            $table->boolean('is_active')->default(true)->index();
        });

        DB::table('training_levels')->insert([
            ['id' => 'duoi_3_thang', 'name' => 'Dưới 3 tháng', 'display_order' => 1, 'is_active' => true],
            ['id' => 'so_cap', 'name' => 'Sơ cấp', 'display_order' => 2, 'is_active' => true],
            ['id' => 'trung_cap', 'name' => 'Trung cấp', 'display_order' => 3, 'is_active' => true],
            ['id' => 'cao_dang', 'name' => 'Cao đẳng', 'display_order' => 4, 'is_active' => true],
        ]);

        Schema::table('workforce_demands', function (Blueprint $table): void {
            $table->foreign('education_level_id')
                ->references('id')
                ->on('training_levels')
                ->restrictOnDelete();
        });

        Schema::table('training_capacities', function (Blueprint $table): void {
            $table->foreign('education_level_id')
                ->references('id')
                ->on('training_levels')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('training_capacities', function (Blueprint $table): void {
            $table->dropForeign(['education_level_id']);
        });

        Schema::table('workforce_demands', function (Blueprint $table): void {
            $table->dropForeign(['education_level_id']);
        });

        Schema::dropIfExists('training_levels');
    }
};
