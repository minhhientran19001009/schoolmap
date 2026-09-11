<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Remove ordering now that campus members are listed alphabetically. */
    public function up(): void
    {
        // Preserve existing locations while mapping retired labels to the
        // closest remaining relationship type.
        DB::table('schools')
            ->whereIn('campus_type', ['SATELLITE', 'AFFILIATE'])
            ->update(['campus_type' => 'CAMPUS']);

        Schema::table('schools', function (Blueprint $table) {
            $table->dropForeign('fk_school_parent_campus');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropIndex('idx_school_parent_campus');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn('campus_order');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->index('parent_school_id', 'idx_school_parent_campus');
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
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->unsignedSmallInteger('campus_order')->default(0);
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->index(['parent_school_id', 'campus_order'], 'idx_school_parent_campus');
            $table->foreign('parent_school_id', 'fk_school_parent_campus')
                ->references('id')
                ->on('schools')
                ->restrictOnDelete();
        });
    }
};
