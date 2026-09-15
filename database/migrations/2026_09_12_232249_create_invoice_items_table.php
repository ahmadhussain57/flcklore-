<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('invoice_id')
                ->constrained('invoices')
                ->cascadeOnDelete();

            // المنتج (nullable لأن المنتج قد يُحذف لاحقاً)
            $table->foreignUlid('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            // snapshot لبيانات المنتج
            $table->string('product_title');
            $table->string('product_sku')->nullable();
            $table->string('product_unit', 20)->nullable(); // قطعة | كغ | متر ...

            // الكميات والأسعار
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);

            // ملاحظات
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('invoice_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};