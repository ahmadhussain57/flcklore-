<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleUpgradeRequest;
use App\Models\RoleUpgradeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RoleUpgradeRequestController extends Controller
{
    public function create(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return view('role-upgrade.create', [
            'contentRole' => $user->content_role?->name,
            'marketingRole' => $user->marketing_role?->name,
            'contentRoles' => RoleUpgradeRequest::allowedRolesFor('content'),
            'marketingRoles' => RoleUpgradeRequest::allowedRolesFor('marketing'),
        ]);
    }

    public function store(StoreRoleUpgradeRequest $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $section = $request->validated('section');
        $requestedRole = $request->validated('requested_role');

        // يجب أن يملك دورين أصلًا
        if (! $user->hasBothSectionRoles()) {
            return back()->with('error', 'حسابك غير مكتمل في أحد القسمين.');
        }

        // لا يطلب ترقية إن كان أصلًا مدير هذا القسم
        if (
            ($section === 'content' && $user->hasRole('content_admin')) ||
            ($section === 'marketing' && $user->hasRole('marketing_admin'))
        ) {
            return back()->with('error', 'أنت مدير هذا القسم بالفعل.');
        }

        // منع طلب معلّق مكرر لنفس القسم
        $hasPending = RoleUpgradeRequest::query()
            ->where('user_id', $user->id)
            ->where('section', $section)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->with('error', 'لديك طلب ترقية معلّق لهذا القسم بالفعل.');
        }

        RoleUpgradeRequest::create([
            'user_id' => $user->id,
            'section' => $section,
            'requested_role' => $requestedRole,
            'message' => $request->validated('message'),
            'status' => 'pending',
        ]);

        return redirect()
            ->route('home')
            ->with('success', 'تم إرسال طلب الترقية بنجاح. بانتظار مراجعة المدير.');
    }
}