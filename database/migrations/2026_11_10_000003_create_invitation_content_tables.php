<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->string('timezone', 4)->default('WIB');
            $table->string('venue_name', 150);
            $table->text('address')->nullable();
            $table->string('maps_url', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('invitation_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('caption', 150)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('invitation_stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('title', 150);
            $table->string('story_date', 50)->nullable();
            $table->text('body');
            $table->string('photo_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_stories');
        Schema::dropIfExists('invitation_photos');
        Schema::dropIfExists('invitation_events');
    }
};
