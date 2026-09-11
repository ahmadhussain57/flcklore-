<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->ulid('original_id')->nullable()->index();
            $table->foreign('original_id')
                  ->references('id')
                  ->on('contents')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropForeign(['original_id']);
            $table->dropColumn('original_id');
        });
    }
};