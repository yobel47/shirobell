<?php

use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\InvitationController;
use App\Http\Controllers\Customer\WishExportController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicInvitationController;
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
    });
