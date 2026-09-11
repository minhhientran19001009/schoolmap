<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Keep installations created from earlier database dumps compatible with
     * the campus data exposed by the school API.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('schools', 'campus_note')) {
            Schema::table('schools', function (Blueprint $table) {
                $table->string('campus_note')->nullable()->after('lng');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('schools', 'campus_note')) {
            Schema::table('schools', function (Blueprint $table) {
                $table->dropColumn('campus_note');
            });
        }
    }
};
