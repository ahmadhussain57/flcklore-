<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('contents', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('user_id')
                ->constrained('users')
                ->cascadeOnDelete(); // المؤلف (المدون)

            $table->string('title');
            $table->string('slug')->unique();

            // article | image | video | audio
            $table->string('type', 20);

            $table->longText('body')->nullable(); // للنص/المقال
            $table->text('keywords')->nullable();
            $table->text('historical_importance')->nullable();
            $table->string('geographic_location')->nullable();

            // draft | pending_review | published | rejected | pending_edit
            $table->string('status', 30)->default('draft')->index();

            $table->text('rejection_reason')->nullable();
            $table->timestamp('queued_at')->nullable()->index(); // ترتيب الطابور FIFO
            $table->timestamp('published_at')->nullable();

            $table->foreignUlid('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'queued_at']);
        });

        Schema::create('category_content', function (Blueprint $table) {
            $table->foreignUlid('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->foreignUlid('content_id')
                ->constrained('contents')
                ->cascadeOnDelete();

            $table->primary(['category_id', 'content_id']);
        });

        Schema::create('content_tag', function (Blueprint $table) {
            $table->foreignUlid('content_id')
                ->constrained('contents')
                ->cascadeOnDelete();

            $table->foreignUlid('tag_id')
                ->constrained('tags')
                ->cascadeOnDelete();

            $table->primary(['content_id', 'tag_id']);
        });

        Schema::create('content_media', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('content_id')
                ->constrained('contents')
                ->cascadeOnDelete();

            // image | video | audio | other
            $table->string('media_type', 20);
            $table->string('path'); // مسار الملف أو رابط مؤقت
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
    $table->ulid('id')->primary();

    $table->foreignUlid('user_id')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->text('body');

    // الهدف: Content أو Product لاحقًا
    $table->ulidMorphs('commentable'); // commentable_type + commentable_id + index

    $table->timestamps();
});

Schema::create('likes', function (Blueprint $table) {
    $table->ulid('id')->primary();

    $table->foreignUlid('user_id')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->ulidMorphs('likeable'); // likeable_type + likeable_id + index

    $table->timestamps();

    // إعجاب واحد فقط لكل مستخدم على نفس العنصر
    $table->unique(['user_id', 'likeable_type', 'likeable_id'], 'likes_user_likeable_unique');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('content_media');
        Schema::dropIfExists('content_tag');
        Schema::dropIfExists('category_content');
        Schema::dropIfExists('contents');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('likes');

    }
};