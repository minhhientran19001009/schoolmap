<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Link individual locations into a campus group without merging their operational data. */
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('campus_type', 20)->default('MAIN');
            $table->string('campus_name', 150)->nullable();
            $table->unsignedSmallInteger('campus_order')->default(0);
            $table->string('parent_school_id', 50)->nullable();

            $table->index(['parent_school_id', 'campus_order'], 'idx_school_parent_campus');
            $table->foreign('parent_school_id', 'fk_school_parent_campus')
                ->references('id')
                ->on('schools')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropForeign('fk_school_parent_campus');
            $table->dropIndex('idx_school_parent_campus');
            $table->dropColumn(['parent_school_id', 'campus_order', 'campus_name', 'campus_type']);
        });
    }
};
