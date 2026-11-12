<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            // Free-form canvas designs (ADR 012), e.g. {"cover": {"enabled": true, "elements": [...]}}.
            $table->json('canvas')->nullable()->after('customization');
        });
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('canvas');
        });
    }
};
