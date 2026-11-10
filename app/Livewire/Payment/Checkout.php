<?php

namespace App\Livewire\Payment;

use App\Models\Invitation;
use App\Services\Payment\CheckoutService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Checkout extends Component
{
    #[Locked]
    public Invitation $invitation;

    public string $promoInput = '';

    #[Locked]
    public ?string $appliedPromo = null;

    public function mount(Invitation $invitation): void
    {
        Gate::authorize('update', $invitation);
        $this->invitation = $invitation;
    }

    public function applyPromo(CheckoutService $checkout): void
    {
        $this->validate(['promoInput' => ['required', 'string', 'max:30']], [], ['promoInput' => 'kode promo']);

        try {
            $checkout->quote($this->invitation, $this->promoInput);
        } catch (ValidationException $e) {
            $this->addError('promoInput', $e->validator->errors()->first('promo'));

            return;
        }

        $this->appliedPromo = strtoupper(trim($this->promoInput));
        $this->promoInput = '';
    }

    public function removePromo(): void
    {
        $this->appliedPromo = null;
    }

    public function pay(CheckoutService $checkout)
    {
        $this->invitation->refresh();
        Gate::authorize('update', $this->invitation);

        $order = $checkout->start(auth()->user(), $this->invitation, $this->appliedPromo);

        return $order->canContinuePayment()
            ? $this->redirect($order->payment_url)
            : $this->redirectRoute('app.orders.show', $order);
    }

    public function render(CheckoutService $checkout)
    {
        try {
            $quote = $checkout->quote($this->invitation, $this->appliedPromo);
        } catch (ValidationException $e) {
            // Promo became invalid (quota used up, expired) after it was applied.
            $this->appliedPromo = null;
            $this->addError('promoInput', $e->validator->errors()->first('promo'));
            $quote = $checkout->quote($this->invitation);
        }

        return view('livewire.payment.checkout', ['quote' => $quote]);
    }

    protected function validationAttributes(): array
    {
        return ['promoInput' => 'kode promo'];
    }
}
