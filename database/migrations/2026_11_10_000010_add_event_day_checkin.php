<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            // Event-day module (ADR 011). Set when the add-on is paid (addon mode).
            $table->timestamp('event_day_unlocked_at')->nullable()->after('published_at');
            // Secret part of the receptionist link; null = check-in not enabled.
            $table->string('checkin_token', 40)->nullable()->unique()->after('event_day_unlocked_at');
            // Encrypted 6-digit PIN, shown to the owner.
            $table->text('checkin_pin')->nullable()->after('checkin_token');
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->timestamp('checked_in_at')->nullable()->index();
            $table->unsignedTinyInteger('checked_in_pax')->nullable();
            $table->boolean('is_walk_in')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn(['checked_in_at', 'checked_in_pax', 'is_walk_in']);
        });
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropUnique(['checkin_token']);
            $table->dropColumn(['event_day_unlocked_at', 'checkin_token', 'checkin_pin']);
        });
    }
};
