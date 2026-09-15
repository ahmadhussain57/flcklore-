<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * عرض صفحة Checkout (إتمام الشراء)
     */
    public function checkout(): View|RedirectResponse
    {
        $user = Auth::user();
        $cart = $user->getOrCreateCart();

        // ✅ التحقق: السلة غير فارغة
        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'السلة فارغة. أضف منتجات قبل إتمام الشراء.');
        }

        // ✅ التحقق: لا توجد مشاكل في المخزون
        $cart->load(['items.product']);
        $hasIssues = $cart->items->contains(fn($item) => !$item->hasEnoughStock());

        if ($hasIssues) {
            return redirect()->route('cart.index')
                ->with('error', 'يوجد منتجات بكميات غير متوفرة. يرجى تعديل السلة أولاً.');
        }

        // هل تحتوي السلة على منتجات مادية؟ (لتحديد هل نطلب عنوان شحن)
        $hasPhysical = $cart->hasPhysicalProducts();

        return view('orders.checkout', compact('cart', 'hasPhysical'));
    }

    /**
     * إنشاء الطلب
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $cart = $user->getOrCreateCart();

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'السلة فارغة.');
        }

        $cart->load(['items.product']);

        // التحقق: كل المنتجات لا تزال منشورة ومتوفرة
        foreach ($cart->items as $item) {
            if (!$item->product || $item->product->status !== Product::STATUS_PUBLISHED) {
                return redirect()->route('cart.index')
                    ->with('error', 'أحد المنتجات لم يعد متوفراً. يرجى مراجعة السلة.');
            }
            if (!$item->hasEnoughStock()) {
                return redirect()->route('cart.index')
                    ->with('error', 'أحد المنتجات لا يحتوي على الكمية المطلوبة.');
            }
        }

        // قواعد التحقق
        $hasPhysical = $cart->hasPhysicalProducts();

        $rules = [
            'payment_method' => 'required|in:cash_on_delivery,bank_transfer',
            'notes'          => 'nullable|string|max:1000',
            'shipping_name'  => 'nullable|string|max:100',
            'shipping_phone' => 'nullable|string|max:30',
            'shipping_city'  => 'nullable|string|max:100',
            'shipping_address' => 'nullable|string|max:500',
        ];

        // إذا كانت السلة تحتوي على منتجات مادية → العنوان إجباري
        if ($hasPhysical) {
            $rules['shipping_name']    = 'required|string|max:100';
            $rules['shipping_phone']   = 'required|string|max:30';
            $rules['shipping_city']    = 'required|string|max:100';
            $rules['shipping_address'] = 'required|string|max:500';
        }

        $validated = $request->validate($rules, [
            'payment_method.required' => 'يجب اختيار طريقة الدفع.',
            'payment_method.in'       => 'طريقة الدفع غير صحيحة.',
            'shipping_name.required'  => 'الاسم مطلوب للشحن.',
            'shipping_phone.required' => 'رقم الهاتف مطلوب للشحن.',
            'shipping_city.required'  => 'المدينة مطلوبة للشحن.',
            'shipping_address.required' => 'العنوان مطلوب للشحن.',
        ]);

        // ✅ إنشاء الطلب في transaction
        $order = DB::transaction(function () use ($cart, $user, $validated, $hasPhysical) {
            // 1. إنشاء الطلب
            $order = Order::create([
                'order_number'    => Order::generateOrderNumber(),
                'user_id'         => $user->id,
                'status'          => Order::STATUS_PENDING,
                'subtotal'        => $cart->subtotal,
                'shipping_cost'   => 0,
                'total'           => $cart->subtotal,
                'payment_method'  => $validated['payment_method'],
                'payment_status'  => Order::PAYMENT_STATUS_UNPAID,
                'notes'           => $validated['notes'] ?? null,
                'shipping_name'   => $validated['shipping_name'] ?? null,
                'shipping_phone'  => $validated['shipping_phone'] ?? null,
                'shipping_city'   => $validated['shipping_city'] ?? null,
                'shipping_address'=> $validated['shipping_address'] ?? null,
                'placed_at'       => now(),
            ]);

            // 2. نسخ عناصر السلة إلى order_items (مع snapshot)
            foreach ($cart->items as $item) {
                $product = $item->product;

                $order->items()->create([
                    'product_id'    => $product->id,
                    'product_title' => $product->title,
                    'product_price' => $product->effective_price,
                    'product_type'  => $product->type,
                    'product_sku'   => $product->sku,
                    'quantity'      => $item->quantity,
                    'subtotal'      => $product->effective_price * $item->quantity,
                ]);

                // 3. خصم من المخزون (للمنتجات المادية فقط)
                if ($product->isPhysical()) {
                    $product->decrement('stock_quantity', $item->quantity);
                }
            }

            // 4. تفريغ السلة
            $cart->items()->delete();

            return $order;
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'تم إنشاء طلبك بنجاح! رقم الطلب: ' . $order->order_number);
    }

    /**
     * عرض طلبات المستخدم
     */
    public function index(): View
    {
        $user = Auth::user();

        $orders = Order::with(['items'])
            ->where('user_id', $user->id)
            ->orderByDesc('placed_at')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * عرض تفاصيل طلب
     */
    public function show(Order $order): View
    {
        $user = Auth::user();

        // ✅ التحقق: المستخدم يملك الطلب (أو مدير/محاسب)
        $canViewAny = $user->hasAnyRole(['marketing_admin', 'marketing_Accountant']);
        if ($order->user_id !== $user->id && !$canViewAny) {
            abort(403, 'غير مصرح لك بعرض هذا الطلب.');
        }

        $order->load(['items.product', 'user']);

        return view('orders.show', compact('order'));
    }

    /**
     * إلغاء طلب
     */
    public function cancel(Order $order): RedirectResponse
    {
        $user = Auth::user();

        if ($order->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بإلغاء هذا الطلب.');
        }

        if (!$order->canBeCancelled()) {
            return back()->with('error', 'لا يمكن إلغاء هذا الطلب في حالته الحالية.');
        }

        DB::transaction(function () use ($order) {
            // إعادة الكميات للمخزون (للمنتجات المادية)
            foreach ($order->items as $item) {
                if ($item->isPhysical() && $item->product) {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
            }

            $order->update([
                'status' => Order::STATUS_CANCELLED,
            ]);
        });

        return back()->with('success', 'تم إلغاء الطلب بنجاح.');
    }
}