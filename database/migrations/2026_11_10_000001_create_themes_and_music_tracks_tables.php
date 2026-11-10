<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('name', 100);
            $table->string('description')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('badge', 20)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('music_tracks', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->string('artist', 150)->nullable();
            $table->string('file_path');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('music_tracks');
        Schema::dropIfExists('themes');
    }
};
