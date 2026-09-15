<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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
        $request->authenticate();

        $request->session()->regenerate();

        // ✅ التحقق من redirect_to
        $redirectTo = $request->input('redirect_to');

        if ($redirectTo && $this->isSafeRedirect($redirectTo)) {
            return redirect()->to($redirectTo);
        }

        return redirect()->intended(route('home', absolute: false));
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

    /**
     * ✅ التحقق من أن الرابط آمن (نفس الموقع)
     */
    private function isSafeRedirect(string $url): bool
    {
        // فقط الروابط الداخلية (تبدأ بـ APP_URL)
        if (!Str::startsWith($url, url('/'))) {
            return false;
        }

        // منع التوجيه لصفحات auth (لتجنب الحلقة اللانهائية)
        if (Str::contains($url, ['/login', '/register', '/logout'])) {
            return false;
        }

        return true;
    }
}