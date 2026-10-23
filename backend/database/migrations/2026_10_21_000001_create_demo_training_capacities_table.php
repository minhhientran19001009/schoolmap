<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_training_capacities', function (Blueprint $table): void {
            $table->id();
            $table->string('school_id', 50);
            $table->foreignId('training_major_id')->constrained('training_majors')->restrictOnDelete();
            $table->string('education_level_id', 20);
            $table->foreignId('reporting_period_id')->constrained('reporting_periods')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->string('location', 255);
            $table->string('duration', 120)->nullable();
            $table->json('skills');
            $table->text('equipment')->nullable();
            $table->text('note')->nullable();
            $table->string('status', 20)->default('ACTIVE')->index();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->cascadeOnDelete();
            $table->foreign('education_level_id')->references('id')->on('training_levels')->restrictOnDelete();
            $table->unique(
                ['school_id', 'training_major_id', 'education_level_id', 'reporting_period_id'],
                'demo_training_capacities_scope_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_training_capacities');
    }
};
