<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds an optional catalogue code only. Existing majors and assignments
     * remain untouched, and no historical JSON data is rewritten.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('training_majors', 'code')) {
            Schema::table('training_majors', function (Blueprint $table): void {
                $table->string('code', 50)->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('training_majors', 'code')) {
            Schema::table('training_majors', function (Blueprint $table): void {
                $table->dropIndex(['code']);
                $table->dropColumn('code');
            });
        }
    }
};
