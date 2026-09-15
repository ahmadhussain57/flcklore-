<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // رقم الطلب (مثل ORD-2026-0001)
            $table->string('order_number', 30)->unique();

            // صاحب الطلب
            $table->foreignUlid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // الحالة
            // pending | paid | processing | shipped | cancelled
            $table->string('status', 20)->default('pending')->index();

            // المبالغ
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // الدفع
            // cash_on_delivery | bank_transfer | stripe | paypal
            $table->string('payment_method', 30)->default('cash_on_delivery');
            // unpaid | paid | refunded
            $table->string('payment_status', 20)->default('unpaid')->index();

            // ملاحظات العميل
            $table->text('notes')->nullable();

            // معلومات الشحن (للمنتجات المادية)
            $table->string('shipping_name')->nullable();
            $table->string('shipping_phone', 30)->nullable();
            $table->string('shipping_city')->nullable();
            $table->text('shipping_address')->nullable();

            // التواريخ
            $table->timestamp('placed_at')->useCurrent();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            // فهارس للبحث
            $table->index(['user_id', 'status']);
            $table->index('placed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};