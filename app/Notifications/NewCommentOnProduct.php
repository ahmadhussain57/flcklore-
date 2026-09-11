<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCommentOnProduct extends Notification
{
    use Queueable;

    public function __construct(
        public Product $product,
        public Comment $comment
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => '💬 تعليق جديد على منتجك',
            'message' => "علّق {$this->comment->user->name} على «{$this->product->title}»",
            'url'     => route('shop.show', $this->product->slug) . '#comments',
            'icon'    => '💬',
            'color'   => 'info',
            'product_id' => $this->product->id,
            'comment_id' => $this->comment->id,
        ];
    }
}