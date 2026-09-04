<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Handle the incoming login request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Verifikasi Cloudflare Turnstile Captcha
        if (! app()->environment('testing')) {
            $turnstileResponse = $request->input('cf-turnstile-response');

            if (! $turnstileResponse) {
                return back()->withErrors([
                    'captcha' => 'Verifikasi Cloudflare Turnstile diperlukan.',
                ])->onlyInput('email');
            }

            $siteverify = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => config('services.turnstile.secret'),
                'response' => $turnstileResponse,
                'remoteip' => $request->ip(),
            ]);

            if (! $siteverify->json('success')) {
                return back()->withErrors([
                    'captcha' => 'Verifikasi captcha Cloudflare gagal. Silakan coba lagi.',
                ])->onlyInput('email');
            }
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
