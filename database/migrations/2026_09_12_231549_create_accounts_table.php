<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // رقم الحساب (1001, 2001, ...)
            $table->string('code', 20)->unique();

            // اسم الحساب
            $table->string('name');
            $table->string('name_en')->nullable();

            // نوع الحساب: asset | liability | equity | revenue | expense
            $table->string('type', 20)->index();

            // طبيعة الرصيد: debit | credit
            $table->string('normal_balance', 10);

            // الوصف
            $table->text('description')->nullable();

            // حسابات النظام لا يمكن حذفها
            $table->boolean('is_system')->default(false);

            // حالة الحساب
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};