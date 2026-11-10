<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            // Owner opt-in for photos at check-in (ADR 011, stage C).
            $table->boolean('checkin_selfie')->default(false)->after('checkin_pin');
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->string('selfie_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn('selfie_path');
        });
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('checkin_selfie');
        });
    }
};
