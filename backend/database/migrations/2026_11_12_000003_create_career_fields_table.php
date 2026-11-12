<?php

use Database\Seeders\CareerFieldSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('career_fields')) {
            Schema::create('career_fields', function (Blueprint $table): void {
                $table->string('id', 60)->primary();
                $table->string('name', 150)->unique();
                $table->string('icon', 80)->nullable();
                $table->text('description')->nullable();
                $table->unsignedSmallInteger('display_order')->default(1)->index();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        (new CareerFieldSeeder)->run();
    }

    public function down(): void
    {
        Schema::dropIfExists('career_fields');
    }
};
