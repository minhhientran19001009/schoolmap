<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_source_records', function (Blueprint $table): void {
            $table->id();
            $table->string('source_key', 100)->unique();
            $table->string('record_type', 30)->index();
            $table->string('school_id', 50)->nullable()->index();
            $table->string('match_status', 30)->index();
            $table->string('source_name', 255);
            $table->string('content_hash', 64);
            $table->json('payload');
            $table->timestamps();
            $table->foreign('school_id')->references('id')->on('schools')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_source_records');
    }
};
