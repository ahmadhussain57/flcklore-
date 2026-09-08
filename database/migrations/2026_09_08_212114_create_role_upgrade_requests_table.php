<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('role_upgrade_requests', function (Blueprint $table) {
        $table->ulid('id')->primary();

        $table->foreignUlid('user_id')
            ->constrained('users')
            ->cascadeOnDelete();

        $table->enum('section', ['content', 'marketing']);
        $table->string('requested_role');
        $table->text('message')->nullable();

        $table->enum('status', ['pending', 'approved', 'rejected'])
            ->default('pending');

        $table->text('admin_note')->nullable();
        $table->foreignUlid('reviewed_by')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();
        $table->timestamp('reviewed_at')->nullable();

        $table->timestamps();

        $table->index(['section', 'status']);
        $table->index(['user_id', 'status']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_upgrade_requests');
    }
};
