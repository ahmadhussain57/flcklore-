<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * عرض محتوى السلة
     */
    public function index(): View
    {
        $user = Auth::user();
        $cart = $user->getOrCreateCart();

        $cart->load(['items.product.media', 'items.product.categories']);

        // التحقق من توفر المخزون لكل عنصر
        $cart->items->each(function ($item) {
            $item->stock_ok = $item->hasEnoughStock();
        });

        $hasIssues = $cart->items->contains(fn($item) => !$item->stock_ok);

        return view('cart.index', compact('cart', 'hasIssues'));
    }

    /**
     * إضافة منتج إلى السلة
     */
    public function add(Request $request, Product $product): RedirectResponse
    {
        $user = Auth::user();

        // ✅ التحقق: المنتج منشور
        if ($product->status !== Product::STATUS_PUBLISHED) {
            return back()->with('error', 'هذا المنتج غير متوفر حالياً.');
        }

        // ✅ التحقق: المخزون للمنتجات المادية
        if ($product->isPhysical() && !$product->isInStock()) {
            return back()->with('error', 'نفد مخزون هذا المنتج.');
        }

        // ✅ التحقق: المستخدم لا يشتري منتجه الخاص
        if ($product->user_id === $user->id) {
            return back()->with('error', 'لا يمكنك شراء منتجك الخاص.');
        }

        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1|max:99',
        ]);

        $requestedQuantity = $validated['quantity'] ?? 1;
        $quantity = $requestedQuantity;
        $wasLimited = false;

        $cart = $user->getOrCreateCart();

        // ✅ التحقق من المخزون الكافي (في حال التراكم)
        $existingItem = $cart->items()->where('product_id', $product->id)->first();
        $existingQuantity = $existingItem?->quantity ?? 0;
        $totalQuantity = $existingQuantity + $requestedQuantity;

        if ($product->isPhysical() && $product->stock_quantity < $totalQuantity) {
            $available = $product->stock_quantity - $existingQuantity;

            // إذا كان المستخدم قد وصل بالفعل للحد الأقصى
            if ($available <= 0) {
                return back()->with('error', "لا يمكن إضافة المزيد من «{$product->title}». لديك بالفعل {$existingQuantity} قطعة في السلة (الحد الأقصى المتوفر).");
            }

            // ✅ ضبط الكمية للحد المتوفر
            $quantity = $available;
            $wasLimited = true;
        }

        // ✅ إضافة أو تحديث الكمية
        DB::transaction(function () use ($cart, $product, $quantity) {
            $item = $cart->items()->where('product_id', $product->id)->first();

            if ($item) {
                $item->increment('quantity', $quantity);
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                ]);
            }
        });

        // ✅ رسالة النتيجة
        if ($wasLimited) {
            $message = "⚠️ تمت إضافة {$quantity} قطعة فقط من «{$product->title}» — الكمية المتوفرة في المخزون محدودة ({$product->stock_quantity} قطعة).";

            if ($existingQuantity > 0) {
                $message .= " (لديك بالفعل {$existingQuantity} قطعة في السلة)";
            }

            return redirect()
                ->route('cart.index')
                ->with('warning', $message);
        }

        return redirect()
            ->route('cart.index')
            ->with('success', "تمت إضافة «{$product->title}» إلى السلة.");
    }

    /**
     * تحديث كمية عنصر في السلة
     */
    public function update(Request $request, CartItem $item): RedirectResponse
    {
        $user = Auth::user();

        // ✅ التحقق من الملكية
        if ($item->cart->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بتعديل هذه السلة.');
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ], [
            'quantity.required' => 'يجب تحديد الكمية.',
            'quantity.min'      => 'الحد الأدنى للكمية هو 1.',
            'quantity.max'      => 'الحد الأقصى للكمية هو 99.',
        ]);

        $quantity = $validated['quantity'];
        $product = $item->product;
        $currentQuantity = $item->quantity;

        // ✅ التحقق من المخزون (رسالة أوضح + ذكر الكمية الحالية)
        if ($product && $product->isPhysical() && $product->stock_quantity < $quantity) {
            $message = "⚠️ لا يمكن تحديث الكمية إلى {$quantity} — الكمية المتوفرة من «{$product->title}» هي {$product->stock_quantity} فقط.";

            if ($currentQuantity > 0) {
                $message .= " (الكمية الحالية في السلة: {$currentQuantity})";
            }

            return back()->with('error', $message);
        }

        // إذا كانت الكمية الجديدة تساوي الحالية → لا داعي لإظهار رسالة نجاح
        if ($quantity === $currentQuantity) {
            return back();
        }

        $item->update(['quantity' => $quantity]);

        return back()->with('success', "✅ تم تحديث كمية «{$product->title}» إلى {$quantity} قطعة.");
    }

    /**
     * إزالة عنصر من السلة
     */
    public function remove(CartItem $item): RedirectResponse
    {
        $user = Auth::user();

        if ($item->cart->user_id !== $user->id) {
            abort(403, 'غير مصرح لك بتعديل هذه السلة.');
        }

        $productTitle = $item->product?->title ?? 'المنتج';
        $item->delete();

        return back()->with('success', "تمت إزالة «{$productTitle}» من السلة.");
    }

    /**
     * تفريغ السلة بالكامل
     */
    public function clear(): RedirectResponse
    {
        $user = Auth::user();
        $cart = $user->getOrCreateCart();

        $cart->items()->delete();

        return redirect()
            ->route('cart.index')
            ->with('success', 'تم تفريغ السلة.');
    }

    /**
     * جلب عدد العناصر في السلة (API للـ Badge)
     */
    public function count(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['count' => 0]);
        }

        $cart = $user->cart;
        $count = $cart ? $cart->total_items : 0;

        return response()->json(['count' => $count]);
    }
}