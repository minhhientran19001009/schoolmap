<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('training_capacities') && ! Schema::hasColumn('training_capacities', 'location')) {
            Schema::table('training_capacities', function (Blueprint $table): void {
                $table->string('location', 255)->nullable()->after('quantity');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('training_capacities') && Schema::hasColumn('training_capacities', 'location')) {
            Schema::table('training_capacities', function (Blueprint $table): void {
                $table->dropColumn('location');
            });
        }
    }
};
