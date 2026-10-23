<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_demand_proposals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('demo_workforce_demand_id')->constrained('demo_workforce_demands')->cascadeOnDelete();
            $table->foreignId('demo_training_capacity_id')->constrained('demo_training_capacities')->cascadeOnDelete();
            $table->string('school_id', 50);
            $table->unsignedInteger('quantity');
            $table->text('plan');
            $table->string('status', 20)->default('SUBMITTED')->index();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->cascadeOnDelete();
            $table->unique(
                ['demo_workforce_demand_id', 'demo_training_capacity_id'],
                'demo_demand_proposals_scope_unique',
            );
            $table->index(['school_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_demand_proposals');
    }
};
