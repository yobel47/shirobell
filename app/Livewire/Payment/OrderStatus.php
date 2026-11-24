<?php

namespace App\Livewire\Payment;

use App\Models\Order;
use App\Services\Payment\CheckoutService;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Throwable;

/**
 * Order page (also Midtrans' finish URL). It never trusts redirect query
 * parameters; it only asks Midtrans' status API (PaymentService::sync).
 */
class OrderStatus extends Component
{
    #[Locked]
    public Order $order;

    public ?string $notice = null;

    public function mount(Order $order): void
    {
        Gate::authorize('view', $order);
        $this->order = $order;

        if ($order->isPending() && $order->payment_url) {
            $this->syncQuietly();
        }
    }

    public function check(): void
    {
        Gate::authorize('view', $this->order);

        $key = 'order-sync:'.$this->order->id;
        if (RateLimiter::tooManyAttempts($key, 6)) {
            $this->notice = 'Terlalu sering. Coba lagi sebentar lagi.';

            return;
        }
        RateLimiter::hit($key, 60);

        $this->notice = $this->syncQuietly()
            ? ($this->order->isPending() ? 'Pembayaran belum kami terima.' : null)
            : 'Belum bisa menghubungi Midtrans. Coba lagi sebentar lagi.';
    }

    /**
     * Cancel the current transaction and start a new one, so the customer can
     * pick another method in Snap (Snap locks the method once chosen).
     */
    public function changeMethod(CheckoutService $checkout): void
    {
        Gate::authorize('view', $this->order);

        $key = 'order-restart:'.auth()->id();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->notice = 'Terlalu sering mengganti metode. Coba lagi beberapa menit lagi.';

            return;
        }
        RateLimiter::hit($key, 600);

        try {
            $order = $checkout->restart(auth()->user(), $this->order);
        } catch (ValidationException $e) {
            $this->order->refresh();
            $this->notice = $e->validator->errors()->first();

            return;
        }

        if ($order->canContinuePayment()) {
            $this->redirect($order->payment_url);

            return;
        }

        $this->redirectRoute('app.orders.show', $order);
    }

    /** Light polling while pending: reads the database only. */
    public function refreshFromDatabase(): void
    {
        $this->order->refresh();
    }

    public function render()
    {
        return view('livewire.payment.order-status', [
            'invitation' => $this->order->invitation,
        ]);
    }

    private function syncQuietly(): bool
    {
        try {
            $this->order = app(PaymentService::class)->sync($this->order);

            return true;
        } catch (Throwable $e) {
            Log::warning('Midtrans status check failed', ['order' => $this->order->number, 'error' => $e->getMessage()]);

            return false;
        }
    }
}
