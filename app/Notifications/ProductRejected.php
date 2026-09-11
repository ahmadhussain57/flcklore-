<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProductRejected extends Notification
{
    use Queueable;

    public function __construct(
        public Product $product,
        public string $reason
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => '❌ تم رفض منتجك',
            'message' => "تم رفض «{$this->product->title}». السبب: {$this->reason}",
            'url'     => route('products.edit', $this->product),
            'icon'    => '❌',
            'color'   => 'danger',
            'product_id' => $this->product->id,
            'reason'  => $this->reason,
        ];
    }
}