<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProductApproved extends Notification
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
            'title'   => '✅ تم نشر منتجك',
            'message' => "تمت الموافقة على «{$this->product->title}» وهو الآن معروض في المتجر.",
            'url'     => route('products.show', $this->product),
            'icon'    => '✅',
            'color'   => 'success',
            'product_id' => $this->product->id,
        ];
    }
}