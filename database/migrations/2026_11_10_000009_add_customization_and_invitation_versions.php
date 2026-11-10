<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            // Per-invitation design overrides, e.g. {"font_pair": "anggun"}.
            $table->json('customization')->nullable()->after('theme_variant');
        });

        // Restorable snapshots of an invitation's content (ADR 010).
        Schema::create('invitation_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reason', 20); // auto | manual | before_restore
            $table->json('snapshot');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['invitation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_versions');
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('customization');
        });
    }
};
