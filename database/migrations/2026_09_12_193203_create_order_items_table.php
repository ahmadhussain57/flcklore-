<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            // ✅ حقل product_id nullable لأن المنتج قد يُحذف لاحقاً
            // لكن نحتفظ بنسخة من بياناته
            $table->foreignUlid('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            // snapshot لبيانات المنتج وقت الطلب
            $table->string('product_title');
            $table->decimal('product_price', 10, 2);
            $table->string('product_type', 20); // physical | digital
            $table->string('product_sku')->nullable();

            // الكمية والمجموع
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 10, 2);

            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};