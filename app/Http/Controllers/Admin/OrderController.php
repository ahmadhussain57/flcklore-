<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Services\AccountingService;
use App\Models\JournalEntry;
use App\Services\InvoiceService;

class OrderController extends Controller
{
    /**
     * عرض كل الطلبات (مع فلاتر)
     */
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'items']);

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // فلترة حسب حالة الدفع
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        // بحث برقم الطلب أو اسم العميل
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // فلترة حسب التاريخ
        if ($request->filled('from_date')) {
            $query->whereDate('placed_at', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('placed_at', '<=', $request->input('to_date'));
        }

        // الترتيب
        $sort = $request->input('sort', 'latest');
        $query = match ($sort) {
            'oldest'     => $query->oldest('placed_at'),
            'total_high' => $query->orderByDesc('total'),
            'total_low'  => $query->orderBy('total'),
            default      => $query->latest('placed_at'),
        };

        $orders = $query->paginate(20)->withQueryString();

        // إحصائيات
        $stats = [
            'total'          => Order::count(),
            'pending'        => Order::where('status', Order::STATUS_PENDING)->count(),
            'paid'           => Order::where('payment_status', Order::PAYMENT_STATUS_PAID)->count(),
            'processing'     => Order::where('status', Order::STATUS_PROCESSING)->count(),
            'shipped'        => Order::where('status', Order::STATUS_SHIPPED)->count(),
            'cancelled'      => Order::where('status', Order::STATUS_CANCELLED)->count(),
            'total_revenue'  => Order::where('payment_status', Order::PAYMENT_STATUS_PAID)->sum('total'),
            'today_orders'   => Order::whereDate('placed_at', today())->count(),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * عرض تفاصيل طلب
     */
    public function show(Order $order): View
    {
        $order->load(['user', 'items.product']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * تحديث حالة الطلب
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,cancelled',
        ]);

        $newStatus = $validated['status'];

        // ✅ التحقق من صحة الانتقال بين الحالات
        if (!$this->isValidTransition($order->status, $newStatus)) {
            return back()->with('error', 'لا يمكن الانتقال من الحالة الحالية إلى هذه الحالة.');
        }

        DB::transaction(function () use ($order, $newStatus) {
            $order->update(['status' => $newStatus]);

            // إذا تم الشحن، نسجّل وقت الشحن
            if ($newStatus === Order::STATUS_SHIPPED) {
                // يمكن إضافة حقل shipped_at لاحقاً
            }

            // إذا كان الطلب ملغى، نُعيد المخزون (إن لم يُرجَع سابقاً)
            if ($newStatus === Order::STATUS_CANCELLED && $order->status !== Order::STATUS_CANCELLED) {
                foreach ($order->items as $item) {
                    if ($item->isPhysical() && $item->product) {
                        $item->product->increment('stock_quantity', $item->quantity);
                    }
                }
            }
        });

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
    }

    /**
     * تأكيد الدفع (يُولّد سند قيد لاحقاً)
     */
       /**
     * تأكيد الدفع (مع توليد سند قيد + فاتورة تلقائياً)
     */
    public function confirmPayment(
        Order $order,
        AccountingService $accountingService,
        InvoiceService $invoiceService
    ): RedirectResponse {
        if ($order->payment_status === Order::PAYMENT_STATUS_PAID) {
            return back()->with('error', 'هذا الطلب مدفوع مسبقاً.');
        }

        if ($order->status === Order::STATUS_CANCELLED) {
            return back()->with('error', 'لا يمكن تأكيد دفع طلب ملغى.');
        }

        // ✅ 1. تحديث حالة الطلب
        $order->update([
            'payment_status' => Order::PAYMENT_STATUS_PAID,
            'paid_at'        => now(),
            'status'         => Order::STATUS_PAID,
        ]);

        // ✅ 2. توليد سند قيد تلقائياً
        $journalEntryNumber = null;
        $journalError = null;

        try {
            $entry = $accountingService->generateJournalEntryFromOrder($order);
            $journalEntryNumber = $entry->entry_number;
        } catch (\Exception $e) {
            $journalError = $e->getMessage();
            \Log::warning("فشل توليد سند قيد للطلب {$order->order_number}: " . $e->getMessage());
        }

        // ✅ 3. توليد فاتورة تلقائياً
        $invoiceNumber = null;
        $invoiceError = null;

        try {
            $invoice = $invoiceService->generateFromOrder($order);
            $invoiceNumber = $invoice->invoice_number;
        } catch (\Exception $e) {
            $invoiceError = $e->getMessage();
            \Log::warning("فشل توليد فاتورة للطلب {$order->order_number}: " . $e->getMessage());
        }

        // ✅ 4. رسالة النتيجة
        $message = 'تم تأكيد الدفع بنجاح.';
        if ($journalEntryNumber) {
            $message .= " سند القيد: {$journalEntryNumber}.";
        }
        if ($invoiceNumber) {
            $message .= " الفاتورة: {$invoiceNumber}.";
        }

        if ($journalError || $invoiceError) {
            $message .= ' (ملاحظة: بعض السجلات لم تُنشأ تلقائياً — راجع السجلات)';
        }

        return back()->with('success', $message);
    }

    /**
     * إلغاء طلب
     */
    public function cancel(Order $order): RedirectResponse
    {
        if ($order->status === Order::STATUS_CANCELLED) {
            return back()->with('error', 'هذا الطلب ملغى مسبقاً.');
        }

        if ($order->status === Order::STATUS_SHIPPED) {
            return back()->with('error', 'لا يمكن إلغاء طلب تم شحنه.');
        }

        DB::transaction(function () use ($order) {
            // إرجاع المخزون
            foreach ($order->items as $item) {
                if ($item->isPhysical() && $item->product) {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
            }

            $order->update(['status' => Order::STATUS_CANCELLED]);
        });

        return back()->with('success', 'تم إلغاء الطلب بنجاح.');
    }

    /**
     * التحقق من صحة الانتقال بين الحالات
     */
    private function isValidTransition(string $currentStatus, string $newStatus): bool
    {
        // الحالات المسموح بها من كل حالة
        $transitions = [
            Order::STATUS_PENDING    => [Order::STATUS_PAID, Order::STATUS_CANCELLED],
            Order::STATUS_PAID       => [Order::STATUS_PROCESSING, Order::STATUS_CANCELLED],
            Order::STATUS_PROCESSING => [Order::STATUS_SHIPPED, Order::STATUS_CANCELLED],
            Order::STATUS_SHIPPED    => [], // لا يمكن التغيير بعد الشحن
            Order::STATUS_CANCELLED  => [], // لا يمكن التغيير بعد الإلغاء
        ];

        return in_array($newStatus, $transitions[$currentStatus] ?? []);
    }
    public function generateJournalEntry(Order $order, AccountingService $service): RedirectResponse
{
    try {
        $entry = $service->generateJournalEntryFromOrder($order);

        return back()->with('success', "تم توليد سند القيد رقم {$entry->entry_number} بنجاح.");
    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }

}
}