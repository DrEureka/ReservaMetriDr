<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $turnstileResponse = $request->input('cf-turnstile-response');
        if (! $turnstileResponse) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => __('Verificación anti-bot requerida.'),
            ]);
        }

        $verification = Http::withHeaders([
            'Accept' => 'application/json',
        ])->asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('services.turnstile.secret'),
            'response' => $turnstileResponse,
            'remoteip' => $request->ip(),
        ]);

        if (! $verification->json('success', false)) {
            throw ValidationException::withMessages([
                'cf-turnstile-response' => __('Verificación anti-bot fallida. Intentá de nuevo.'),
            ]);
        }

        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
