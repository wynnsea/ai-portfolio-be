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
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('issuer');
            $table->string('category', 20)->index();
            $table->string('year', 30)->nullable();
            $table->string('score', 50)->nullable();
            $table->string('note')->nullable();
            $table->string('credential_url')->nullable();
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
        Schema::dropIfExists('certifications');
    }
};
