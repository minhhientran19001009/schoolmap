<?php

use Database\Seeders\SkillCatalogSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('skills')) {
            return;
        }

        if (! Schema::hasColumn('skills', 'career_field')) {
            Schema::table('skills', function (Blueprint $table): void {
                $table->string('career_field', 60)->nullable()->index()->after('normalized_name');
            });
        }

        (new SkillCatalogSeeder)->run();
    }

    public function down(): void
    {
        if (! Schema::hasTable('skills') || ! Schema::hasColumn('skills', 'career_field')) {
            return;
        }

        Schema::table('skills', function (Blueprint $table): void {
            $table->dropIndex(['career_field']);
            $table->dropColumn('career_field');
        });
    }
};
