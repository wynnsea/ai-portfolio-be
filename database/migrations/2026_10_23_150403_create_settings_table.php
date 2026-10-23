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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('brand', 80)->nullable();
            $table->string('name', 80)->nullable();
            $table->string('name_jp', 50)->nullable();
            $table->string('role', 80)->nullable();
            $table->text('description')->nullable();
            $table->string('location', 80)->nullable();
            $table->string('email')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('cv_path')->nullable();
            $table->json('availability')->nullable();
            $table->json('socials')->nullable();
            $table->json('features')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
