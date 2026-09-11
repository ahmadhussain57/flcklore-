<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProductSubmittedForReview extends Notification
{
    use Queueable;

    public function __construct(public Product $product)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => '📦 منتج جديد بانتظار المراجعة',
            'message' => "قدّم {$this->product->author->name} منتجاً بعنوان: «{$this->product->title}»",
            'url'     => route('products.review.preview', $this->product),
            'icon'    => '📦',
            'color'   => 'warning',
            'product_id' => $this->product->id,
        ];
    }
}