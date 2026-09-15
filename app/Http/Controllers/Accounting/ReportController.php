<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\MaterialMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Exports\LedgerExport;
use App\Exports\MaterialMovementExport;
use App\Exports\WarehouseExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * الصفحة الرئيسية للتقارير
     */
    public function index(): View
    {
        $stats = [
            'total_entries' => JournalEntry::count(),
            'total_invoices' => Invoice::count(),
            'total_movements' => MaterialMovement::count(),
            'total_accounts' => Account::count(),
        ];

        return view('accounting.reports.index', compact('stats'));
    }

    /**
     * تقرير حركة المواد
     */
    public function materialMovement(Request $request): View
    {
        $query = MaterialMovement::with(['product', 'creator']);

        // فلترة حسب المنتج
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        // فلترة حسب النوع
        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->input('movement_type'));
        }

        // فلترة حسب التاريخ
        if ($request->filled('from_date')) {
            $query->whereDate('movement_date', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('movement_date', '<=', $request->input('to_date'));
        }

        $movements = $query->latest('movement_date')->paginate(30)->withQueryString();

        // إحصائيات
        $stats = [
            'total_in' => (clone $query)->where('movement_type', MaterialMovement::TYPE_PURCHASE)->sum('quantity')
                + (clone $query)->where('movement_type', MaterialMovement::TYPE_RETURN)->sum('quantity'),
            'total_out' => abs((clone $query)->where('movement_type', MaterialMovement::TYPE_SALE)->sum('quantity')),
            'total_adjustments' => (clone $query)->where('movement_type', MaterialMovement::TYPE_ADJUSTMENT)->count(),
            'total_cost' => (clone $query)->sum('total_cost'),
        ];

        $products = Product::orderBy('title')->get(['id', 'title']);

        return view('accounting.reports.material-movement', compact('movements', 'stats', 'products'));
    }

    /**
     * تقرير دفتر الأستاذ
     */
    public function ledger(Request $request): View
    {
        // الحساب المحدد
        $accountId = $request->input('account_id');
        $selectedAccount = $accountId ? Account::find($accountId) : null;

        $entries = collect();
        $balance = 0;

        if ($selectedAccount) {
            $query = $selectedAccount->journalEntryLines()
                ->with(['journalEntry'])
                ->whereHas('journalEntry', function ($q) use ($request) {
                    if ($request->filled('from_date')) {
                        $q->whereDate('entry_date', '>=', $request->input('from_date'));
                    }
                    if ($request->filled('to_date')) {
                        $q->whereDate('entry_date', '<=', $request->input('to_date'));
                    }
                })
                ->orderBy('created_at');

            $lines = $query->get();

            // حساب الرصيد التراكمي
            $balance = 0;
            $entries = $lines->map(function ($line) use (&$balance, $selectedAccount) {
                if ($selectedAccount->normal_balance === Account::BALANCE_DEBIT) {
                    $balance += (float) $line->debit - (float) $line->credit;
                } else {
                    $balance += (float) $line->credit - (float) $line->debit;
                }

                return [
                    'date' => $line->journalEntry->entry_date,
                    'entry_number' => $line->journalEntry->entry_number,
                    'description' => $line->description ?? $line->journalEntry->description,
                    'debit' => (float) $line->debit,
                    'credit' => (float) $line->credit,
                    'balance' => $balance,
                ];
            });
        }

        $accounts = Account::where('is_active', true)->orderBy('code')->get();

        return view('accounting.reports.ledger', compact('accounts', 'selectedAccount', 'entries', 'balance'));
    }

    /**
     * تقرير حالة المستودع
     */
    public function warehouse(Request $request): View
    {
        $query = Product::where('type', Product::TYPE_PHYSICAL);

        // فلترة حسب التصنيف
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('product_categories.id', $request->input('category_id'));
            });
        }

        // فلترة "نفد المخزون"
        if ($request->boolean('out_of_stock')) {
            $query->where('stock_quantity', '<=', 0);
        }

        // فلترة "مخزون منخفض" (أقل من 5)
        if ($request->boolean('low_stock')) {
            $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 5);
        }

        $products = $query->with(['categories', 'media'])->orderBy('title')->paginate(30)->withQueryString();

        // إحصائيات
        $stats = [
            'total_products' => Product::where('type', Product::TYPE_PHYSICAL)->count(),
            'total_quantity' => Product::where('type', Product::TYPE_PHYSICAL)->sum('stock_quantity'),
            'total_value' => Product::where('type', Product::TYPE_PHYSICAL)
                ->selectRaw('SUM(stock_quantity * price) as total')
                ->value('total') ?? 0,
            'out_of_stock' => Product::where('type', Product::TYPE_PHYSICAL)->where('stock_quantity', '<=', 0)->count(),
            'low_stock' => Product::where('type', Product::TYPE_PHYSICAL)->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 5)->count(),
        ];

        $categories = \App\Models\ProductCategory::orderBy('name')->get();

        return view('accounting.reports.warehouse', compact('products', 'stats', 'categories'));
    }

        /**
     * تصدير تقرير حركة المواد إلى Excel
     */
    public function materialMovementExcel(Request $request)
    {
        $filters = $request->only(['product_id', 'movement_type', 'from_date', 'to_date']);
        $filename = 'material-movement-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new MaterialMovementExport($filters), $filename);
    }

    /**
     * تصدير دفتر الأستاذ إلى Excel
     */
    public function ledgerExcel(Request $request)
    {
        $accountId = $request->input('account_id');
        $selectedAccount = $accountId ? Account::find($accountId) : null;

        if (!$selectedAccount) {
            return back()->with('error', 'يجب اختيار حساب أولاً.');
        }

        $query = $selectedAccount->journalEntryLines()
            ->with(['journalEntry'])
            ->whereHas('journalEntry', function ($q) use ($request) {
                if ($request->filled('from_date')) {
                    $q->whereDate('entry_date', '>=', $request->input('from_date'));
                }
                if ($request->filled('to_date')) {
                    $q->whereDate('entry_date', '<=', $request->input('to_date'));
                }
            })
            ->orderBy('created_at');

        $lines = $query->get();
        $balance = 0;

        $entries = $lines->map(function ($line) use (&$balance, $selectedAccount) {
            if ($selectedAccount->normal_balance === Account::BALANCE_DEBIT) {
                $balance += (float) $line->debit - (float) $line->credit;
            } else {
                $balance += (float) $line->credit - (float) $line->debit;
            }

            return [
                'date' => $line->journalEntry->entry_date,
                'entry_number' => $line->journalEntry->entry_number,
                'description' => $line->description ?? $line->journalEntry->description,
                'debit' => (float) $line->debit,
                'credit' => (float) $line->credit,
                'balance' => $balance,
            ];
        })->toArray();

        $filename = 'ledger-' . $selectedAccount->code . '-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new LedgerExport($selectedAccount, $entries), $filename);
    }

    /**
     * تصدير حالة المستودع إلى Excel
     */
    public function warehouseExcel(Request $request)
    {
        $filters = $request->only(['category_id', 'out_of_stock', 'low_stock']);
        $filename = 'warehouse-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new WarehouseExport($filters), $filename);
    }
}