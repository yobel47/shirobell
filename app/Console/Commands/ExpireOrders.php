<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Console\Command;

class ExpireOrders extends Command
{
    protected $signature = 'orders:expire';

    protected $description = 'Mark pending orders past expires_at as expired';

    public function handle(): int
    {
        // A late Midtrans settlement can still mark an expired order paid (PaymentService).
        $count = Order::query()
            ->where('status', OrderStatus::Pending)
            ->where('expires_at', '<', now()->subHour())
            ->update(['status' => OrderStatus::Expired->value, 'updated_at' => now()]);

        $this->info("{$count} order kedaluwarsa.");

        return self::SUCCESS;
    }
}
