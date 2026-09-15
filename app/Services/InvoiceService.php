<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * إنشاء فاتورة من طلب مدفوع
     *
     * @throws \Exception
     */
    public function generateFromOrder(Order $order): Invoice
    {
        // التحقق: الطلب مدفوع
        if ($order->payment_status !== Order::PAYMENT_STATUS_PAID) {
            throw new \Exception('لا يمكن إنشاء فاتورة لطلب غير مدفوع.');
        }

        // التحقق: هل يوجد فاتورة مسبقاً؟
        $existing = Invoice::where('order_id', $order->id)->first();
        if ($existing) {
            throw new \Exception("يوجد فاتورة مسبقاً لهذا الطلب: {$existing->invoice_number}");
        }

        return DB::transaction(function () use ($order) {
            $subtotal = $order->items->sum(fn($i) => $i->product_price * $i->quantity);

            $invoice = Invoice::create([
                'invoice_number'  => Invoice::generateInvoiceNumber(),
                'type'            => Invoice::TYPE_SALE_RETAIL,
                'invoice_date'    => $order->paid_at ?? now(),
                'user_id'         => $order->user_id,
                'party_name'      => $order->shipping_name ?? $order->user->name,
                'party_phone'     => $order->shipping_phone,
                'party_address'   => $order->shipping_address,
                'order_id'        => $order->id,
                'payment_status'  => Invoice::PAYMENT_PAID,
                'payment_type'    => Invoice::PAYMENT_TYPE_CASH,
                'subtotal'        => $subtotal,
                'discount'        => 0,
                'tax'             => 0,
                'shipping_cost'   => $order->shipping_cost ?? 0,
                'total'           => $order->total,
                'paid_amount'     => $order->total,
                'remaining_amount'=> 0,
                'notes'           => "فاتورة تلقائية من الطلب #{$order->order_number}",
                'created_by'      => Auth::id(),
            ]);

            foreach ($order->items as $item) {
                $invoice->items()->create([
                    'product_id'    => $item->product_id,
                    'product_title' => $item->product_title,
                    'product_sku'   => $item->product_sku,
                    'quantity'      => $item->quantity,
                    'unit_price'    => $item->product_price,
                    'discount'      => 0,
                    'subtotal'      => $item->subtotal,
                ]);
            }

            return $invoice;
        });
    }
}