<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // رقم الفاتورة (INV-2026-0001)
            $table->string('invoice_number', 30)->unique();

            // نوع الفاتورة
            // purchase_wholesale | purchase_retail
            // sale_wholesale     | sale_retail     | sale_return
            $table->string('type', 30)->index();

            // تاريخ الفاتورة
            $table->date('invoice_date')->index();

            // تاريخ الاستحقاق (للأجل)
            $table->date('due_date')->nullable();

            // الطرف الآخر (مورد أو عميل)
            $table->foreignUlid('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // اسم الطرف (snapshot - للفواتير اليدوية)
            $table->string('party_name')->nullable();
            $table->string('party_phone', 30)->nullable();
            $table->text('party_address')->nullable();

            // ربط بالطلب (إن كانت فاتورة مبيع من طلب)
            $table->foreignUlid('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();

            // حالة الدفع
            // unpaid | partial | paid
            $table->string('payment_status', 20)->default('unpaid')->index();

            // طريقة الدفع
            // cash | credit
            $table->string('payment_type', 20)->default('cash');

            // المبالغ
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // المدفوع والمتبقي
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('remaining_amount', 12, 2)->default(0);

            // ملاحظات
            $table->text('notes')->nullable();

            // من أنشأ الفاتورة
            $table->foreignUlid('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // فهارس
            $table->index(['type', 'invoice_date']);
            $table->index(['payment_status', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};