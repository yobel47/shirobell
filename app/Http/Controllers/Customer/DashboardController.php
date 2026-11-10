<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $invitations = Invitation::ownedBy($request->user())
            ->with('theme')
            ->latest()
            ->get();

        return view('customer.dashboard', [
            'invitations' => $invitations,
            'canCreate' => $invitations->where('status.value', 'draft')->count()
                < (int) config('shirobell.invitation.max_drafts_per_user', 5),
        ]);
    }
}
