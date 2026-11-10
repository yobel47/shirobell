<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('phone', 20)->nullable();
            $table->string('group_name', 50)->nullable();
            $table->string('code', 12);
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('shared_at')->nullable();
            $table->timestamps();

            $table->unique(['invitation_id', 'code']);
        });

        Schema::create('wishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 100);
            $table->string('attendance', 10)->nullable();
            $table->unsignedTinyInteger('pax')->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_hidden')->default(false);
            $table->string('ip_hash', 64)->nullable();
            $table->timestamps();

            $table->index(['invitation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishes');
        Schema::dropIfExists('guests');
    }
};
