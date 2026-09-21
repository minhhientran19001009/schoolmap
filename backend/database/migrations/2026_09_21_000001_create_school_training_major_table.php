<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_training_major', function (Blueprint $table): void {
            $table->id();
            $table->string('school_id', 50);
            $table->foreignId('training_major_id')->constrained('training_majors')->cascadeOnDelete();
            $table->string('degree_level', 30)->default('cao_dang');
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->cascadeOnDelete();
            $table->unique(['school_id', 'training_major_id', 'degree_level'], 'school_training_major_unique');
            $table->index(['school_id', 'training_major_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_training_major');
    }
};
