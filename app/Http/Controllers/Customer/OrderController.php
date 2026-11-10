<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\Order;
use App\Services\Payment\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        return view('customer.orders.index', [
            'orders' => Order::query()
                ->where('user_id', $request->user()->id)
                ->with('invitation:id,slug,groom_nickname,bride_nickname')
                ->latest()
                ->paginate(20),
        ]);
    }

    public function show(Order $order): View
    {
        Gate::authorize('view', $order);

        return view('customer.orders.show', ['order' => $order]);
    }

    public function checkout(Request $request, Invitation $invitation, CheckoutService $checkout): View|RedirectResponse
    {
        Gate::authorize('update', $invitation);

        $product = $request->query('produk') === 'hari-h' ? CheckoutService::EVENT_DAY : CheckoutService::PACKAGE;
        if ($product === CheckoutService::EVENT_DAY && ! $checkout->canBuyEventDay($invitation)) {
            return redirect()->route('app.invitations.event-day', $invitation);
        }

        return view('customer.orders.checkout', ['invitation' => $invitation, 'product' => $product]);
    }
}
