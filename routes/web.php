<?php

use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
| Main domain routes. Invitation subdomain routes ({slug}.APP_DOMAIN) are
| registered separately in M2 (routes/invitation.php).
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/health', HealthController::class)->name('health');

// Admin temporarily allowed until the Filament panel exists (M5).
Route::middleware(['auth', 'verified', 'role:customer,admin'])
    ->prefix('app')
    ->name('app.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
    });
