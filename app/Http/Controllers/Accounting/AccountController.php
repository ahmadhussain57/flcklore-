<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AccountController extends Controller
{
    /**
     * عرض جميع الحسابات مع أرصدتها
     */
    public function index(Request $request): View
    {
        $query = Account::query();

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // بحث بالاسم أو الكود
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        $accounts = $query->orderBy('code')->get();

        // حساب الأرصدة لكل حساب
        // ✅ استعلام واحد لجلب كل الإجماليات
        $totals = JournalEntryLine::select(
                'account_id',
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(credit) as total_credit')
            )
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        // إضافة الأرصدة لكل حساب
        $accounts = $accounts->map(function ($account) use ($totals) {
            $total = $totals->get($account->id);

            $debit = (float) ($total->total_debit ?? 0);
            $credit = (float) ($total->total_credit ?? 0);

            // حساب الرصيد حسب طبيعة الحساب
            if ($account->normal_balance === Account::BALANCE_DEBIT) {
                $balance = $debit - $credit;
            } else {
                $balance = $credit - $debit;
            }

            $account->total_debit = $debit;
            $account->total_credit = $credit;
            $account->balance = $balance;
            $account->entries_count = JournalEntryLine::where('account_id', $account->id)->count();

            return $account;
        });

        // إحصائيات عامة
        $stats = [
            'total_accounts'   => Account::count(),
            'assets'           => Account::where('type', Account::TYPE_ASSET)->count(),
            'liabilities'      => Account::where('type', Account::TYPE_LIABILITY)->count(),
            'revenues'         => Account::where('type', Account::TYPE_REVENUE)->count(),
            'expenses'         => Account::where('type', Account::TYPE_EXPENSE)->count(),
        ];

        return view('accounting.accounts.index', compact('accounts', 'stats'));
    }

    /**
     * عرض تفاصيل حساب واحد
     */
    public function show(Account $account): View
    {
        // جلب كل السطور
        $lines = $account->journalEntryLines()
            ->with(['journalEntry'])
            ->orderBy('created_at')
            ->paginate(30);

        // حساب الرصيد
        $totalDebit  = (float) $account->journalEntryLines()->sum('debit');
        $totalCredit = (float) $account->journalEntryLines()->sum('credit');

        $balance = $account->normal_balance === Account::BALANCE_DEBIT
            ? $totalDebit - $totalCredit
            : $totalCredit - $totalDebit;

        return view('accounting.accounts.show', compact('account', 'lines', 'balance', 'totalDebit', 'totalCredit'));
    }
}