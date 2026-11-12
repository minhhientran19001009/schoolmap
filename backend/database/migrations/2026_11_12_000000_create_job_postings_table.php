<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employers', function (Blueprint $table): void {
            $table->string('public_id', 100)->nullable()->unique();
            $table->json('public_profile')->nullable();
        });
        Schema::create('job_postings', function (Blueprint $table): void {
            $table->string('id', 100)->primary();
            $table->foreignId('employer_id')->constrained('employers')->restrictOnDelete();
            $table->string('title', 255);
            $table->string('industry', 60)->nullable()->index();
            $table->unsignedInteger('recruitment_count')->default(1);
            $table->date('deadline')->nullable()->index();
            $table->string('status', 20)->default('PUBLISHED')->index();
            $table->json('details');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_postings');
        Schema::table('employers', function (Blueprint $table): void {
            $table->dropColumn(['public_id', 'public_profile']);
        });
    }
};
