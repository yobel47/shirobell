<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // Invitation list is added in M2 (Invitation module).
        return view('customer.dashboard');
    }
}
