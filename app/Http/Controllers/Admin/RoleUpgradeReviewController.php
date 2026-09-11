<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoleUpgradeRequest;
use App\Notifications\RoleUpgradeReviewed;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RoleUpgradeReviewController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();

        $sections = [];
        if ($admin->hasRole('content_admin')) {
            $sections[] = 'content';
        }
        if ($admin->hasRole('marketing_admin')) {
            $sections[] = 'marketing';
        }

        $requests = RoleUpgradeRequest::query()
            ->with('user')
            ->whereIn('section', $sections ?: ['__none__'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.role-upgrade.index', compact('requests'));
    }

    public function approve(RoleUpgradeRequest $roleUpgradeRequest): RedirectResponse
    {
        $this->authorizeSectionAdmin($roleUpgradeRequest->section);

        if (! $roleUpgradeRequest->isPending()) {
            return back()->with('error', 'هذا الطلب تمت معالجته مسبقًا.');
        }

        // ✅ حفظ المستخدم صاحب الطلب قبل المعالجة
        $applicant = $roleUpgradeRequest->user;

        DB::transaction(function () use ($roleUpgradeRequest) {
            $roleUpgradeRequest->user->assignSectionRole(
                $roleUpgradeRequest->section,
                $roleUpgradeRequest->requested_role
            );

            $roleUpgradeRequest->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);
        });

        // ✅ إرسال إشعار للمستخدم (بعد نجاح المعاملة)
        $applicant->notify(new RoleUpgradeReviewed($roleUpgradeRequest));

        return back()->with('success', 'تمت الموافقة على الطلب وتعيين الدور.');
    }

    public function reject(Request $request, RoleUpgradeRequest $roleUpgradeRequest): RedirectResponse
    {
        $this->authorizeSectionAdmin($roleUpgradeRequest->section);

        if (! $roleUpgradeRequest->isPending()) {
            return back()->with('error', 'هذا الطلب تمت معالجته مسبقًا.');
        }

        $request->validate([
            'admin_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $roleUpgradeRequest->update([
            'status' => 'rejected',
            'admin_note' => $request->input('admin_note'),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // ✅ إرسال إشعار للمستخدم بالرفض
        $roleUpgradeRequest->user->notify(new RoleUpgradeReviewed($roleUpgradeRequest));

        return back()->with('success', 'تم رفض الطلب.');
    }

    private function authorizeSectionAdmin(string $section): void
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();

        $allowed = match ($section) {
            'content' => $admin->hasRole('content_admin'),
            'marketing' => $admin->hasRole('marketing_admin'),
            default => false,
        };

        abort_unless($allowed, 403, 'غير مصرح لك بمراجعة طلبات هذا القسم.');
    }
}