<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            // ✅ UUID بدلاً من ULID (Laravel يولّد UUID تلقائياً للإشعارات)
            $table->uuid('id')->primary();

            $table->string('type');
            $table->ulidMorphs('notifiable'); // ← يبقى ULID لأن User.id هو ULID

            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};