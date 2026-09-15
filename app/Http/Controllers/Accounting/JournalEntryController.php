<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JournalEntryController extends Controller
{
    /**
     * عرض كل سندات القيد
     */
    public function index(Request $request): View
    {
        $query = JournalEntry::with(['creator', 'lines.account']);

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // فلترة حسب التاريخ
        if ($request->filled('from_date')) {
            $query->whereDate('entry_date', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('entry_date', '<=', $request->input('to_date'));
        }

        // بحث برقم السند أو الوصف
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('entry_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // الترتيب
        $sort = $request->input('sort', 'latest');
        $query = match ($sort) {
            'oldest' => $query->oldest('entry_date'),
            'debit_high' => $query->orderByDesc('total_debit'),
            default => $query->latest('entry_date'),
        };

        $entries = $query->paginate(20)->withQueryString();

        // إحصائيات
        $stats = [
            'total' => JournalEntry::count(),
            'cash' => JournalEntry::where('type', JournalEntry::TYPE_CASH)->count(),
            'credit' => JournalEntry::where('type', JournalEntry::TYPE_CREDIT)->count(),
            'adjustment' => JournalEntry::where('type', JournalEntry::TYPE_ADJUSTMENT)->count(),
            'total_debit' => JournalEntry::sum('total_debit'),
        ];

        return view('accounting.journal-entries.index', compact('entries', 'stats'));
    }

    /**
     * عرض نموذج إنشاء سند جديد
     */
    public function create(): View
    {
        $accounts = Account::where('is_active', true)
            ->orderBy('code')
            ->get();

        return view('accounting.journal-entries.create', compact('accounts'));
    }

    /**
     * حفظ سند جديد
     */
    public function store(Request $request): RedirectResponse
    {
        // التحقق الأساسي
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'type' => 'required|in:cash,credit,adjustment',
            'description' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.description' => 'nullable|string|max:255',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
        ], [
            'entry_date.required' => 'تاريخ السند مطلوب.',
            'type.required' => 'نوع السند مطلوب.',
            'description.required' => 'البيان مطلوب.',
            'lines.required' => 'يجب إضافة سطرين على الأقل.',
            'lines.min' => 'يجب إضافة سطرين على الأقل.',
            'lines.*.account_id.required' => 'يجب اختيار الحساب لكل سطر.',
            'lines.*.account_id.exists' => 'الحساب غير موجود.',
        ]);

        // التحقق من أن كل سطر له مبلغ (مدين أو دائن)
        foreach ($validated['lines'] as $index => $line) {
            $debit = (float) ($line['debit'] ?? 0);
            $credit = (float) ($line['credit'] ?? 0);

            if ($debit === 0.0 && $credit === 0.0) {
                return back()->withInput()->withErrors([
                    "lines.{$index}.debit" => 'يجب إدخال مبلغ مدين أو دائن في السطر ' . ($index + 1),
                ]);
            }

            if ($debit > 0 && $credit > 0) {
                return back()->withInput()->withErrors([
                    "lines.{$index}.debit" => 'لا يمكن إدخال مدين ودائن في نفس السطر (' . ($index + 1) . ')',
                ]);
            }
        }

        // حساب الإجماليات
        $totalDebit = collect($validated['lines'])->sum(fn($l) => (float) ($l['debit'] ?? 0));
        $totalCredit = collect($validated['lines'])->sum(fn($l) => (float) ($l['credit'] ?? 0));

        // التحقق من التوازن
        if (bccomp((string) $totalDebit, (string) $totalCredit, 2) !== 0) {
            return back()->withInput()->withErrors([
                'balance' => "السند غير متوازن! المدين: {$totalDebit} - الدائن: {$totalCredit}",
            ]);
        }

        // إنشاء السند في transaction
        $entry = DB::transaction(function () use ($validated, $totalDebit, $totalCredit) {
            $entry = JournalEntry::create([
                'entry_number' => JournalEntry::generateEntryNumber(),
                'entry_date' => $validated['entry_date'],
                'type' => $validated['type'],
                'description' => $validated['description'],
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
                'created_by' => Auth::id(),
                'is_posted' => true,
                'posted_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['lines'] as $line) {
                $entry->lines()->create([
                    'account_id' => $line['account_id'],
                    'description' => $line['description'] ?? null,
                    'debit' => (float) ($line['debit'] ?? 0),
                    'credit' => (float) ($line['credit'] ?? 0),
                ]);
            }

            return $entry;
        });

        return redirect()
            ->route('accounting.journal-entries.show', $entry)
            ->with('success', "تم إنشاء السند رقم {$entry->entry_number} بنجاح.");
    }

    /**
     * عرض تفاصيل سند
     */
    public function show(JournalEntry $journalEntry): View
    {
        $journalEntry->load(['creator', 'lines.account']);

        return view('accounting.journal-entries.show', compact('journalEntry'));
    }

    /**
     * حذف سند (فقط إن لم يكن مُرحَّلاً)
     */
    public function destroy(JournalEntry $journalEntry): RedirectResponse
    {
        // لا يمكن حذف السندات المُرحَّلة (المُرحَّلة نهائية)
        if ($journalEntry->is_posted) {
            return back()->with('error', 'لا يمكن حذف سند مُرحَّل. يجب عكس القيد بسند جديد.');
        }

        DB::transaction(function () use ($journalEntry) {
            $journalEntry->lines()->delete();
            $journalEntry->delete();
        });

        return redirect()
            ->route('accounting.journal-entries.index')
            ->with('success', 'تم حذف السند بنجاح.');
    }
}