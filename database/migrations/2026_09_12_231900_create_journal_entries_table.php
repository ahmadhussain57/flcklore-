<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // رقم السند (JE-2026-0001)
            $table->string('entry_number', 30)->unique();

            // تاريخ السند
            $table->date('entry_date')->index();

            // النوع: cash (نقداً) | credit (أجل) | adjustment (تسوية)
            $table->string('type', 20)->default('cash')->index();

            // البيان / الوصف
            $table->text('description');

            // المرجع (Order, Invoice, ...)
            $table->string('reference_type')->nullable();
            $table->ulid('reference_id')->nullable();

            // الإجماليات
            $table->decimal('total_debit', 12, 2)->default(0);
            $table->decimal('total_credit', 12, 2)->default(0);

            // من أنشأ السند
            $table->foreignUlid('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // هل تم ترحيله؟
            $table->boolean('is_posted')->default(false)->index();
            $table->timestamp('posted_at')->nullable();

            // ملاحظات
            $table->text('notes')->nullable();

            $table->timestamps();

            // فهارس
            $table->index(['reference_type', 'reference_id']);
            $table->index(['entry_date', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};