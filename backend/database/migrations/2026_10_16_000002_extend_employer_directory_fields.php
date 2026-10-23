<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employers', function (Blueprint $table): void {
            $table->json('aliases')->nullable()->after('name');
            $table->string('region', 150)->nullable()->after('industry');
            $table->unsignedInteger('employee_count')->nullable()->after('region');
            $table->string('source_code', 50)->nullable()->after('employee_count');
            $table->text('scope')->nullable()->after('website');
            $table->text('data_note')->nullable()->after('scope');
            $table->json('source_details')->nullable()->after('data_note');
            $table->date('reviewed_at')->nullable()->after('source_details');
        });
    }

    public function down(): void
    {
        Schema::table('employers', function (Blueprint $table): void {
            $table->dropColumn([
                'aliases', 'region', 'employee_count', 'source_code', 'scope',
                'data_note', 'source_details', 'reviewed_at',
            ]);
        });
    }
};
