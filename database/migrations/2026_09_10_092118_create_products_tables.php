<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ==========================================
        // 1. جدول تصنيفات المنتجات
        // ==========================================
        Schema::create('product_categories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // ==========================================
        // 2. جدول المنتجات
        // ==========================================
        Schema::create('products', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // من أنشأ المنتج (متخصص أو مدير تسويق)
            $table->foreignUlid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('short_description', 500)->nullable();
            $table->longText('description')->nullable();

            // physical | digital
            $table->string('type', 20)->default('physical');

            // الأسعار (باستخدام decimal لدقة الحسابات المالية)
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();

            // المخزون (للمنتجات المادية)
            $table->integer('stock_quantity')->nullable();

            // رمز المنتج (اختياري، مفيد للإدارة)
            $table->string('sku')->nullable()->unique();

            // دورة الحياة (نفس نظام المحتوى)
            // draft | pending_review | published | rejected | pending_edit
            $table->string('status', 30)->default('draft')->index();

            $table->text('rejection_reason')->nullable();
            $table->timestamp('queued_at')->nullable()->index();
            $table->timestamp('published_at')->nullable();

            // المراجعة
            $table->foreignUlid('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            // الحجز المؤقت (لتفادي تضارب المراجعين)
            $table->foreignUlid('locked_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('locked_at')->nullable();

            // ربط النسخة بالمنتج الأصلي (للتعديلات)
            $table->ulid('original_id')->nullable()->index();

            $table->timestamps();

            // فهارس مركبة لتسريع الاستعلامات
            $table->index(['status', 'queued_at']);
            $table->index(['user_id', 'status']);
        });

        // ==========================================
        // 3. جدول وسيط: منتج ↔ تصنيف (Many-to-Many)
        // ==========================================
        Schema::create('product_product_category', function (Blueprint $table) {
            $table->foreignUlid('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignUlid('product_category_id')
                ->constrained('product_categories')
                ->cascadeOnDelete();

            $table->primary(['product_id', 'product_category_id']);
        });

        // ==========================================
        // 4. جدول وسائط المنتجات
        // ==========================================
        Schema::create('product_media', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            // image | video
            $table->string('media_type', 20);
            $table->string('path'); // رابط Cloudinary أو مسار محلي
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_media');
        Schema::dropIfExists('product_product_category');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
    }
};