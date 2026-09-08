<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->isStaff()) {
            return redirect()
                ->route('home')
                ->with('error', 'لوحة التحكم مخصصة لأعضاء الفريق فقط.');
        }

        return view('dashboard');
    }
}