<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->index('name', 'idx_school_name');
            $table->index('ward', 'idx_school_ward');
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropIndex('idx_school_name');
            $table->dropIndex('idx_school_ward');
        });
    }
};
