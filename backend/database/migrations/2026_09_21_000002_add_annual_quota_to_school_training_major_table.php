<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('school_training_major', 'annual_quota')) {
            Schema::table('school_training_major', function (Blueprint $table): void {
                $table->unsignedInteger('annual_quota')->default(0)->after('degree_level');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('school_training_major', 'annual_quota')) {
            Schema::table('school_training_major', function (Blueprint $table): void {
                $table->dropColumn('annual_quota');
            });
        }
    }
};
