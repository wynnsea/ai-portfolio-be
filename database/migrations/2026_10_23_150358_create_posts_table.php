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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('category', 30)->index();
            $table->string('status', 10)->default('Draft')->index();
            $table->date('date')->nullable()->index();
            $table->string('read_time', 30)->nullable();
            $table->string('excerpt', 300);
            $table->json('content')->nullable();
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
        Schema::dropIfExists('posts');
    }
};
