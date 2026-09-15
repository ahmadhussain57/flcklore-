<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // إسناد دور الزائر في القسمين معًا — قاعدة المنتج
        $user->assignSectionRole('content', 'content_Guest');
        $user->assignSectionRole('marketing', 'marketing_Guest');

        event(new Registered($user));

        Auth::login($user);

        // ✅ التحقق من redirect_to
        $redirectTo = $request->input('redirect_to');

        if ($redirectTo && $this->isSafeRedirect($redirectTo)) {
            return redirect()->to($redirectTo);
        }

        return redirect(route('home', absolute: false));
    }

    /**
     * ✅ التحقق من أن الرابط آمن (نفس الموقع)
     */
    private function isSafeRedirect(string $url): bool
    {
        if (!Str::startsWith($url, url('/'))) {
            return false;
        }

        if (Str::contains($url, ['/login', '/register', '/logout'])) {
            return false;
        }

        return true;
    }
}