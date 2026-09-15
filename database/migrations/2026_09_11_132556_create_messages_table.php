<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('conversation_id')
                ->constrained('conversations')
                ->cascadeOnDelete();

            // المرسل
            $table->foreignUlid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // نص الرسالة
            $table->text('body');

            // مستقبلاً: مرفقات
            $table->string('attachment_path')->nullable();
            $table->string('attachment_type', 20)->nullable();

            $table->timestamps();

            // تسريع جلب رسائل محادثة معينة مرتبة بالتاريخ
            $table->index(['conversation_id', 'created_at'], 'messages_conversation_created_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};