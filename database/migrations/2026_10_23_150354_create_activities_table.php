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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('role');
            $table->string('organizer')->nullable();
            $table->string('category', 80)->nullable();
            $table->string('year', 30)->nullable();
            $table->text('description')->nullable();
            $table->json('photos')->nullable();
            $table->string('focus', 30)->nullable();
            $table->string('title_jp', 50)->nullable();
            $table->string('kanji', 2)->nullable();
            $table->string('tone', 10)->default('dark');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
