<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_career_field_proposals', function (Blueprint $table): void {
            $table->id();
            $table->string('school_id', 50);
            $table->string('career_field_id', 80);
            $table->unsignedInteger('quantity');
            $table->text('plan');
            $table->string('status', 20)->default('SUBMITTED')->index();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->cascadeOnDelete();
            $table->unique(['school_id', 'career_field_id'], 'demo_field_proposals_school_field_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_career_field_proposals');
    }
};
