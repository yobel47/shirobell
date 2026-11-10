<?php

use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\InvitationController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\WishExportController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalPageController;
use App\Http\Controllers\PublicInvitationController;
use App\Http\Controllers\ThemeCatalogController;
use App\Http\Controllers\ThemeDemoController;
use App\Http\Controllers\Webhook\MidtransNotificationController;
use Illuminate\Support\Facades\Route;

/*
| Invitation subdomain ({slug}.APP_DOMAIN). Only "/" exists there; Livewire's
| own endpoints stay domain-less so the RSVP form works on the subdomain.
*/
Route::domain('{slug}.'.config('app.domain'))
    ->where(['slug' => '[a-z0-9-]+'])
    ->group(function () {
        Route::get('/', PublicInvitationController::class)->name('invitation.show');
    });

/*
| Main domain only. Fortify (config/fortify.php) and the Filament panel are
| bound to the same domain, so /login, /app and /admin never answer on a
| customer's subdomain.
*/
Route::domain(config('app.domain'))->group(function () {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/health', HealthController::class)->name('health');
    Route::get('/tema', ThemeCatalogController::class)->name('themes.index');
    Route::get('/tema/{theme:slug}', ThemeDemoController::class)->name('themes.demo');

    Route::controller(LegalPageController::class)->name('legal.')->group(function () {
        Route::get('/syarat-ketentuan', 'terms')->name('terms');
        Route::get('/kebijakan-privasi', 'privacy')->name('privacy');
        Route::get('/kebijakan-refund', 'refund')->name('refund');
        Route::get('/kontak', 'contact')->name('contact');
    });

    // Payment gateway notification. CSRF-exempt (bootstrap/app.php); verified by signature + status API.
    Route::post('/webhooks/midtrans', MidtransNotificationController::class)
        ->middleware('throttle:120,1')
        ->name('webhooks.midtrans');

    Route::middleware(['auth', 'verified'])->prefix('app')->name('app.')->group(function () {
        // Customer only. Admins are redirected to the Filament panel at /admin.
        Route::middleware('role:customer')->group(function () {
            Route::get('/', DashboardController::class)->name('dashboard');
            Route::get('/invitations/create', [InvitationController::class, 'create'])->name('invitations.create');

            Route::get('/invitations/{invitation}/checkout', [OrderController::class, 'checkout'])->name('checkout');
            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        });

        // Invitation management: the owner, or an admin helping a customer
        // (InvitationPolicy; admin visits and changes are audited).
        Route::middleware('role:customer,admin')->group(function () {
            Route::get('/invitations/{invitation}/preview', [InvitationController::class, 'preview'])->name('invitations.preview');
            Route::get('/invitations/{invitation}/edit/{tab?}', [InvitationController::class, 'edit'])->name('invitations.edit');
            Route::get('/invitations/{invitation}/tamu', [InvitationController::class, 'guests'])->name('invitations.guests');
            Route::get('/invitations/{invitation}/rsvp', [InvitationController::class, 'wishes'])->name('invitations.wishes');
            Route::get('/invitations/{invitation}/rsvp/export', WishExportController::class)->name('invitations.wishes.export');
        });
    });
});
