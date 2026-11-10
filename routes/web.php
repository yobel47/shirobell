<?php

use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\InvitationController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\WishExportController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicInvitationController;
use App\Http\Controllers\Webhook\MidtransNotificationController;
use Illuminate\Support\Facades\Route;

/*
| Invitation subdomain ({slug}.APP_DOMAIN). Registered first so it wins over
| the main-domain routes below, which have no domain constraint.
*/
Route::domain('{slug}.'.config('app.domain'))
    ->where(['slug' => '[a-z0-9-]+'])
    ->group(function () {
        Route::get('/', PublicInvitationController::class)->name('invitation.show');
    });

Route::get('/', HomeController::class)->name('home');
Route::get('/health', HealthController::class)->name('health');

// Payment gateway notification. CSRF-exempt (bootstrap/app.php); verified by signature + status API.
Route::post('/webhooks/midtrans', MidtransNotificationController::class)
    ->middleware('throttle:120,1')
    ->name('webhooks.midtrans');

// Admin temporarily allowed until the Filament panel exists (M5).
Route::middleware(['auth', 'verified', 'role:customer,admin'])
    ->prefix('app')
    ->name('app.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');
        Route::get('/invitations/{invitation}/preview', [InvitationController::class, 'preview'])->name('invitations.preview');
        Route::get('/invitations/{invitation}/edit/{tab?}', [InvitationController::class, 'edit'])->name('invitations.edit');
        Route::get('/invitations/{invitation}/tamu', [InvitationController::class, 'guests'])->name('invitations.guests');
        Route::get('/invitations/{invitation}/rsvp', [InvitationController::class, 'wishes'])->name('invitations.wishes');
        Route::get('/invitations/{invitation}/rsvp/export', WishExportController::class)->name('invitations.wishes.export');

        Route::get('/invitations/{invitation}/checkout', [OrderController::class, 'checkout'])->name('checkout');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    });
