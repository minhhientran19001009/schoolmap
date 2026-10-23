<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_workforce_demands', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employer_id')->constrained('employers')->cascadeOnDelete();
            $table->foreignId('training_major_id')->constrained('training_majors')->restrictOnDelete();
            $table->string('education_level_id', 20);
            $table->foreignId('reporting_period_id')->constrained('reporting_periods')->restrictOnDelete();
            $table->string('purpose', 30)->default('RECRUITMENT');
            $table->unsignedInteger('quantity');
            $table->string('location', 200);
            $table->string('age_range', 80)->nullable();
            $table->string('salary', 100)->nullable();
            $table->json('skills');
            $table->text('note')->nullable();
            $table->string('status', 20)->default('ACTIVE')->index();
            $table->timestamps();

            $table->foreign('education_level_id')->references('id')->on('training_levels')->restrictOnDelete();
            $table->unique(
                ['employer_id', 'training_major_id', 'education_level_id', 'reporting_period_id', 'purpose'],
                'demo_workforce_demands_scope_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_workforce_demands');
    }
};
