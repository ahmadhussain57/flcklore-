<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JournalEntry;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * عرض كل الفواتير
     */
    public function index(Request $request): View
    {
        $query = Invoice::with(['user', 'creator', 'order']);

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // فلترة حسب حالة الدفع
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        // فلترة حسب التاريخ
        if ($request->filled('from_date')) {
            $query->whereDate('invoice_date', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('invoice_date', '<=', $request->input('to_date'));
        }

        // بحث برقم الفاتورة أو اسم الطرف
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('party_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // الترتيب
        $sort = $request->input('sort', 'latest');
        $query = match ($sort) {
            'oldest' => $query->oldest('invoice_date'),
            'total_high' => $query->orderByDesc('total'),
            default => $query->latest('invoice_date'),
        };

        $invoices = $query->paginate(20)->withQueryString();

        // إحصائيات
        $stats = [
            'total' => Invoice::count(),
            'purchases' => Invoice::whereIn('type', [Invoice::TYPE_PURCHASE_WHOLESALE, Invoice::TYPE_PURCHASE_RETAIL])->count(),
            'sales' => Invoice::whereIn('type', [Invoice::TYPE_SALE_WHOLESALE, Invoice::TYPE_SALE_RETAIL])->count(),
            'returns' => Invoice::where('type', Invoice::TYPE_SALE_RETURN)->count(),
            'total_sales' => Invoice::whereIn('type', [Invoice::TYPE_SALE_WHOLESALE, Invoice::TYPE_SALE_RETAIL])->sum('total'),
        ];

        return view('accounting.invoices.index', compact('invoices', 'stats'));
    }

    /**
     * عرض نموذج إنشاء فاتورة جديدة
     */
    public function create(Request $request): View
    {
        $products = Product::where('status', Product::STATUS_PUBLISHED)
            ->orderBy('title')
            ->get();

        $users = User::orderBy('name')->limit(100)->get();

        // النوع الافتراضي
        $defaultType = $request->input('type', Invoice::TYPE_SALE_RETAIL);

        return view('accounting.invoices.create', compact('products', 'users', 'defaultType'));
    }

    /**
     * حفظ فاتورة جديدة
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:purchase_wholesale,purchase_retail,sale_wholesale,sale_retail,sale_return',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'user_id' => 'nullable|exists:users,id',
            'party_name' => 'required|string|max:255',
            'party_phone' => 'nullable|string|max:30',
            'party_address' => 'nullable|string|max:500',
            'payment_type' => 'required|in:cash,credit',
            'payment_status' => 'required|in:unpaid,partial,paid',
            'paid_amount' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_title' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
        ], [
            'type.required' => 'نوع الفاتورة مطلوب.',
            'invoice_date.required' => 'تاريخ الفاتورة مطلوب.',
            'party_name.required' => 'اسم الطرف مطلوب.',
            'items.required' => 'يجب إضافة عنصر واحد على الأقل.',
            'items.min' => 'يجب إضافة عنصر واحد على الأقل.',
        ]);

        // حساب الإجماليات
        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $itemDiscount = (float) ($item['discount'] ?? 0);
            $subtotal += ((float) $item['quantity'] * (float) $item['unit_price']) - $itemDiscount;
        }

        $discount = (float) ($validated['discount'] ?? 0);
        $tax = (float) ($validated['tax'] ?? 0);
        $shipping = (float) ($validated['shipping_cost'] ?? 0);
        $total = $subtotal - $discount + $tax + $shipping;

        $paidAmount = (float) ($validated['paid_amount'] ?? 0);
        $remaining = $total - $paidAmount;

        // تحديث حالة الدفع تلقائياً
        if ($remaining <= 0) {
            $paymentStatus = Invoice::PAYMENT_PAID;
            $paidAmount = $total;
            $remaining = 0;
        } elseif ($paidAmount > 0) {
            $paymentStatus = Invoice::PAYMENT_PARTIAL;
        } else {
            $paymentStatus = Invoice::PAYMENT_UNPAID;
        }

        // إنشاء الفاتورة في transaction
        $invoice = DB::transaction(function () use ($validated, $subtotal, $discount, $tax, $shipping, $total, $paidAmount, $remaining, $paymentStatus) {
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'type' => $validated['type'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'] ?? null,
                'user_id' => $validated['user_id'] ?? null,
                'party_name' => $validated['party_name'],
                'party_phone' => $validated['party_phone'] ?? null,
                'party_address' => $validated['party_address'] ?? null,
                'payment_status' => $paymentStatus,
                'payment_type' => $validated['payment_type'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'shipping_cost' => $shipping,
                'total' => $total,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remaining,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // إضافة العناصر
            foreach ($validated['items'] as $item) {
                $itemDiscount = (float) ($item['discount'] ?? 0);
                $itemSubtotal = ((float) $item['quantity'] * (float) $item['unit_price']) - $itemDiscount;

                $invoice->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'product_title' => $item['product_title'],
                    'product_sku' => $item['product_sku'] ?? null,
                    'product_unit' => $item['product_unit'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $itemDiscount,
                    'subtotal' => $itemSubtotal,
                ]);
            }

            return $invoice;
        });

        return redirect()
            ->route('accounting.invoices.show', $invoice)
            ->with('success', "تم إنشاء الفاتورة {$invoice->invoice_number} بنجاح.");
    }

    /**
     * عرض تفاصيل فاتورة
     */
    public function show(Invoice $invoice): View
    {
        $invoice->load(['items.product', 'user', 'creator', 'order']);

        // البحث عن سند قيد مرتبط
        $journalEntry = JournalEntry::where('reference_type', Invoice::class)
            ->where('reference_id', $invoice->id)
            ->first();

        return view('accounting.invoices.show', compact('invoice', 'journalEntry'));
    }

    /**
     * حذف فاتورة
     */
    public function destroy(Invoice $invoice): RedirectResponse
    {
        // منع حذف فاتورة مرتبطة بسند قيد
        $hasJournalEntry = JournalEntry::where('reference_type', Invoice::class)
            ->where('reference_id', $invoice->id)
            ->exists();

        if ($hasJournalEntry) {
            return back()->with('error', 'لا يمكن حذف فاتورة مرتبطة بسند قيد. احذف السند أولاً.');
        }

        DB::transaction(function () use ($invoice) {
            $invoice->items()->delete();
            $invoice->delete();
        });

        return redirect()
            ->route('accounting.invoices.index')
            ->with('success', 'تم حذف الفاتورة بنجاح.');
    }

    /**
     * إنشاء فاتورة من طلب
     */
    public function createFromOrder(Order $order): RedirectResponse
    {
        // التحقق: هل يوجد فاتورة للطلب مسبقاً؟
        $existingInvoice = Invoice::where('order_id', $order->id)->first();

        if ($existingInvoice) {
            return redirect()
                ->route('accounting.invoices.show', $existingInvoice)
                ->with('info', "يوجد فاتورة مسبقاً لهذا الطلب: {$existingInvoice->invoice_number}");
        }

        // التحقق: الطلب مدفوع
        if ($order->payment_status !== Order::PAYMENT_STATUS_PAID) {
            return back()->with('error', 'لا يمكن إنشاء فاتورة لطلب غير مدفوع.');
        }

        $invoice = DB::transaction(function () use ($order) {
            $subtotal = $order->items->sum(fn($i) => $i->product_price * $i->quantity);

            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'type' => Invoice::TYPE_SALE_RETAIL,
                'invoice_date' => $order->paid_at ?? now(),
                'user_id' => $order->user_id,
                'party_name' => $order->shipping_name ?? $order->user->name,
                'party_phone' => $order->shipping_phone,
                'party_address' => $order->shipping_address,
                'order_id' => $order->id,
                'payment_status' => Invoice::PAYMENT_PAID,
                'payment_type' => Invoice::PAYMENT_TYPE_CASH,
                'subtotal' => $subtotal,
                'discount' => 0,
                'tax' => 0,
                'shipping_cost' => $order->shipping_cost ?? 0,
                'total' => $order->total,
                'paid_amount' => $order->total,
                'remaining_amount' => 0,
                'notes' => "فاتورة من الطلب #{$order->order_number}",
                'created_by' => Auth::id(),
            ]);

            // نسخ عناصر الطلب
            foreach ($order->items as $item) {
                $invoice->items()->create([
                    'product_id' => $item->product_id,
                    'product_title' => $item->product_title,
                    'product_sku' => $item->product_sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->product_price,
                    'discount' => 0,
                    'subtotal' => $item->subtotal,
                ]);
            }

            return $invoice;
        });

        return redirect()
            ->route('accounting.invoices.show', $invoice)
            ->with('success', "تم إنشاء الفاتورة {$invoice->invoice_number} من الطلب بنجاح.");
    }
}