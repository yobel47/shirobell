<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('type', 10);
            $table->unsignedInteger('value');
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('number', 30)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('type', 10);
            $table->unsignedInteger('base_amount');
            $table->unsignedInteger('discount_amount')->default(0);
            $table->unsignedInteger('total_amount');
            $table->foreignId('promo_code_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('duration_days');
            $table->string('status', 20)->default('pending')->index();
            $table->string('snap_token')->nullable();
            $table->string('payment_url', 500)->nullable();
            $table->string('gateway_transaction_id', 100)->nullable()->index();
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_channel', 50)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider', 20);
            $table->string('event', 50)->nullable();
            $table->json('payload');
            $table->boolean('is_processed')->default(false);
            $table->string('error')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::table('invitations', function (Blueprint $table) {
            $table->timestamp('expiry_reminded_at')->nullable()->after('active_until');
        });
    }

    public function down(): void
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropColumn('expiry_reminded_at');
        });
        Schema::dropIfExists('payment_logs');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('promo_codes');
    }
};
