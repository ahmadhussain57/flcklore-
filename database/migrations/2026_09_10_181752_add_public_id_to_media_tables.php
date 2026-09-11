<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // إضافة public_id إلى جدول content_media
        Schema::table('content_media', function (Blueprint $table) {
            if (!Schema::hasColumn('content_media', 'public_id')) {
                $table->string('public_id')->nullable()->after('path');
            }
        });

        // إضافة public_id إلى جدول product_media
        Schema::table('product_media', function (Blueprint $table) {
            if (!Schema::hasColumn('product_media', 'public_id')) {
                $table->string('public_id')->nullable()->after('path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('content_media', function (Blueprint $table) {
            if (Schema::hasColumn('content_media', 'public_id')) {
                $table->dropColumn('public_id');
            }
        });

        Schema::table('product_media', function (Blueprint $table) {
            if (Schema::hasColumn('product_media', 'public_id')) {
                $table->dropColumn('public_id');
            }
        });
    }
};