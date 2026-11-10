<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('theme_id')->constrained()->restrictOnDelete();
            $table->string('slug', 40)->unique();
            $table->string('status', 20)->default('draft')->index();

            foreach (['groom', 'bride'] as $side) {
                $table->string("{$side}_nickname", 50)->nullable();
                $table->string("{$side}_fullname", 150)->nullable();
                $table->string("{$side}_father", 150)->nullable();
                $table->string("{$side}_mother", 150)->nullable();
                $table->string("{$side}_child_order", 50)->nullable();
                $table->string("{$side}_photo_path")->nullable();
                $table->string("{$side}_instagram", 30)->nullable();
            }

            $table->string('cover_photo_path')->nullable();
            $table->text('opening_text')->nullable();
            $table->text('quote_text')->nullable();
            $table->string('quote_source', 150)->nullable();
            $table->text('closing_text')->nullable();
            $table->foreignId('music_track_id')->nullable()->constrained()->nullOnDelete();
            $table->string('custom_music_path')->nullable();
            $table->json('sections')->nullable();
            $table->unsignedTinyInteger('rsvp_max_pax')->default(2);
            $table->text('wa_message_template')->nullable();
            $table->timestamp('active_until')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('suspended_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'active_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
