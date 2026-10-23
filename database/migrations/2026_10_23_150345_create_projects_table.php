<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title', 120);
            $table->string('category', 80);
            $table->string('year', 4);
            $table->string('role', 80)->nullable();
            $table->string('duration', 50)->nullable();
            $table->string('status', 10)->default('Draft')->index();
            $table->text('summary');
            $table->text('overview')->nullable();
            $table->text('challenge')->nullable();
            $table->json('approach')->nullable();
            $table->json('results')->nullable();
            $table->json('stack')->nullable();
            $table->json('links')->nullable();
            $table->string('title_jp', 50)->nullable();
            $table->string('kanji', 2)->nullable();
            $table->string('tone', 10)->default('dark');
            $table->string('cover_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
