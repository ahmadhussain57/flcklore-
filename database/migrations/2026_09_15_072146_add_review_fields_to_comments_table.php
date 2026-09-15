<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // ✅ حالة التعليق: pending | approved | rejected
            $table->string('status', 20)->default('pending')->after('body')->index();

            // ✅ سبب الرفض (إن رُفض)
            $table->text('rejection_reason')->nullable()->after('status');

            // ✅ من راجعه
            $table->foreignUlid('reviewed_by')
                ->nullable()
                ->after('rejection_reason')
                ->constrained('users')
                ->nullOnDelete();

            // ✅ تاريخ المراجعة
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');

            // ✅ فهرس مركّب للاستعلامات السريعة
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropColumn(['status', 'rejection_reason', 'reviewed_by', 'reviewed_at']);
        });
    }
};