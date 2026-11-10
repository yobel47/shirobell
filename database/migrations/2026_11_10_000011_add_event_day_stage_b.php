<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->string('table_number', 20)->nullable();
            $table->timestamp('souvenir_at')->nullable();
        });

        // Gifts recorded at the reception desk (ADR 011, stage B).
        Schema::create('event_gifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->string('giver_name', 100);
            $table->string('kind', 10); // amplop | kado | transfer
            $table->unsignedBigInteger('amount')->nullable();
            $table->string('note', 200)->nullable();
            $table->timestamps();
            $table->index(['invitation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_gifts');
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['table_number', 'souvenir_at']);
        });
    }
};
