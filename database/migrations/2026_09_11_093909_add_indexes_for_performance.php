<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // فهارس جدول contents
        Schema::table('contents', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('published_at');
            $table->index(['type', 'status']);
        });

        // فهارس جدول products
        Schema::table('products', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('published_at');
            $table->index(['type', 'status']);
        });

        // فهارس جدول comments
        Schema::table('comments', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('created_at');
        });

        // فهارس جدول likes
        Schema::table('likes', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['published_at']);
            $table->dropIndex(['type', 'status']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['published_at']);
            $table->dropIndex(['type', 'status']);
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
};