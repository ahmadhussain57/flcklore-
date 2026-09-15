<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // السند المرتبط
            $table->foreignUlid('journal_entry_id')
                ->constrained('journal_entries')
                ->cascadeOnDelete();

            // الحساب
            $table->foreignUlid('account_id')
                ->constrained('accounts')
                ->restrictOnDelete(); // لا نحذف الحساب إن كان مستخدماً

            // وصف السطر (اختياري)
            $table->string('description')->nullable();

            // المبلغ مدين أو دائن (أحدهما صفر)
            $table->decimal('debit', 12, 2)->default(0);
            $table->decimal('credit', 12, 2)->default(0);

            $table->timestamps();

            // فهارس
            $table->index('journal_entry_id');
            $table->index('account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
    }
};