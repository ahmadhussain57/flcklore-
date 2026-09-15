<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('conversation_id')
                ->constrained('conversations')
                ->cascadeOnDelete();

            $table->foreignUlid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // آخر وقت قرأ فيه المستخدم رسائل هذه المحادثة
            $table->timestamp('last_read_at')->nullable();

            $table->timestamps();

            // منع تكرار نفس المستخدم في نفس المحادثة
            $table->unique(['conversation_id', 'user_id'], 'unique_conversation_user');

            // تسريع البحث عن محادثات مستخدم معين
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_participants');
    }
};