<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_movements', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // المنتج/المادة
            $table->foreignUlid('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // نوع الحركة: purchase | sale | return | adjustment
            $table->string('movement_type', 20)->index();

            // الكمية (موجبة للدخول، سالبة للخروج)
            $table->decimal('quantity', 12, 2);

            // رصيد المادة قبل وبعد الحركة (لتتبع الرصيد التاريخي)
            $table->decimal('stock_before', 12, 2)->default(0);
            $table->decimal('stock_after', 12, 2)->default(0);

            // سعر الوحدة وقت الحركة (لحساب التكلفة)
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);

            // المرجع (Order, Invoice, ...)
            $table->string('reference_type')->nullable();
            $table->ulid('reference_id')->nullable();

            // البيان
            $table->text('description')->nullable();

            // تاريخ الحركة
            $table->timestamp('movement_date')->useCurrent()->index();

            // من أنشأ الحركة
            $table->foreignUlid('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // فهارس
            $table->index(['product_id', 'movement_date']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_movements');
    }
};