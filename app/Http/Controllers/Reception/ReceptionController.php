<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Services\EventDayService;
use App\Services\ReceptionAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** Receptionist link (/penerima-tamu/{token}): PIN login, check-in app, welcome screen. */
class ReceptionController extends Controller
{
    public function __construct(
        private readonly EventDayService $eventDay,
        private readonly ReceptionAccess $access,
    ) {}

    public function show(string $token): View
    {
        $invitation = $this->open($token);

        return $this->access->has($invitation)
            ? view('reception.app', ['invitation' => $invitation, 'token' => $token])
            : view('reception.pin', ['invitation' => $invitation, 'token' => $token]);
    }

    public function login(Request $request, string $token): RedirectResponse
    {
        $invitation = $this->open($token);
        $key = 'reception-pin:'.$invitation->id.'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'pin' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.',
            ]);
        }

        $request->validate(['pin' => ['required', 'string', 'max:10']]);

        if (! $this->eventDay->pinMatches($invitation, $request->string('pin')->toString())) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages(['pin' => 'PIN salah.']);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();
        $this->access->grant($invitation);

        return redirect()->route('reception.show', $token);
    }

    public function screen(string $token): View|RedirectResponse
    {
        $invitation = $this->open($token);

        if (! $this->access->has($invitation)) {
            return redirect()->route('reception.show', $token);
        }

        return view('reception.screen', ['invitation' => $invitation, 'token' => $token]);
    }

    public function logout(string $token): RedirectResponse
    {
        $this->access->revoke($this->open($token));

        return redirect()->route('reception.show', $token);
    }

    private function open(string $token): Invitation
    {
        return $this->eventDay->findOpenByToken($token) ?? abort(404);
    }
}
