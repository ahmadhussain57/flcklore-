<?php

namespace App\Services;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * توليد سند قيد لطلب مدفوع
     */
    public function generateJournalEntryFromOrder(Order $order): JournalEntry
    {
        // ✅ التحقق: الطلب مدفوع
        if ($order->payment_status !== Order::PAYMENT_STATUS_PAID) {
            throw new \Exception('لا يمكن توليد سند قيد لطلب غير مدفوع.');
        }

        // ✅ التحقق: هل يوجد سند سابق؟
        $existingEntry = JournalEntry::where('reference_type', Order::class)
            ->where('reference_id', $order->id)
            ->first();

        if ($existingEntry) {
            throw new \Exception('يوجد سند قيد لهذا الطلب مسبقاً: ' . $existingEntry->entry_number);
        }

        return DB::transaction(function () use ($order) {
            // جلب الحسابات
            $cash          = Account::findByCode(Account::CODE_CASH);
            $salesRevenue  = Account::findByCode(Account::CODE_SALES_REVENUE);
            $inventory     = Account::findByCode(Account::CODE_INVENTORY);
            $cogs          = Account::findByCode(Account::CODE_COGS);

            if (!$cash || !$salesRevenue) {
                throw new \Exception('الحسابات الأساسية غير موجودة. تأكد من تشغيل AccountSeeder.');
            }

            // إنشاء السند
            $entry = JournalEntry::create([
                'entry_number'   => JournalEntry::generateEntryNumber(),
                'entry_date'     => now(),
                'type'           => $order->payment_method === Order::PAYMENT_COD
                    ? JournalEntry::TYPE_CASH
                    : JournalEntry::TYPE_CREDIT,
                'description'    => "تأكيد دفع الطلب #{$order->order_number}",
                'reference_type' => Order::class,
                'reference_id'   => $order->id,
                'total_debit'    => 0, // سيُحدَّث بعد إضافة السطور
                'total_credit'   => 0,
                'created_by'     => Auth::id(),
                'is_posted'      => true,
                'posted_at'      => now(),
                'notes'          => "العميل: {$order->user->name}",
            ]);

            // حساب تكلفة البضاعة المباعة (COGS)
            $cogsTotal = 0;
            foreach ($order->items as $item) {
                if ($item->isPhysical() && $item->product) {
                    // تكلفة المنتج: نستخدم السعر الأصلي كتكلفة تقديرية
                    $cogsTotal += ($item->product->price * $item->quantity);
                }
            }

            $lines = [];

            // ✅ سطر 1: الصندوق (مدين)
            $lines[] = [
                'account_id'  => $cash->id,
                'description' => "استلام من الطلب #{$order->order_number}",
                'debit'       => $order->total,
                'credit'      => 0,
            ];

            // ✅ سطر 2: إيرادات المبيعات (دائن)
            $lines[] = [
                'account_id'  => $salesRevenue->id,
                'description' => "إيراد من الطلب #{$order->order_number}",
                'debit'       => 0,
                'credit'      => $order->total,
            ];

            // ✅ إذا كانت هناك منتجات مادية: سطران إضافيان
            if ($cogsTotal > 0 && $inventory && $cogs) {
                // سطر 3: تكلفة البضاعة المباعة (مدين)
                $lines[] = [
                    'account_id'  => $cogs->id,
                    'description' => "تكلفة البضاعة المباعة - الطلب #{$order->order_number}",
                    'debit'       => $cogsTotal,
                    'credit'      => 0,
                ];

                // سطر 4: المخزون (دائن)
                $lines[] = [
                    'account_id'  => $inventory->id,
                    'description' => "خصم من المخزون - الطلب #{$order->order_number}",
                    'debit'       => 0,
                    'credit'      => $cogsTotal,
                ];
            }

            // إضافة السطور
            foreach ($lines as $line) {
                $entry->lines()->create($line);
            }

            // تحديث الإجماليات
            $totalDebit  = collect($lines)->sum('debit');
            $totalCredit = collect($lines)->sum('credit');

            $entry->update([
                'total_debit'  => $totalDebit,
                'total_credit' => $totalCredit,
            ]);

            return $entry;
        });
    }
}