<?php

namespace App\Notifications;

use App\Models\Content;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContentApproved extends Notification
{
    use Queueable;

    public function __construct(public Content $content)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => '✅ تم نشر محتواك',
            'message' => "تمت الموافقة على «{$this->content->title}» ونشره بنجاح.",
            'url'     => route('content.show', $this->content),
            'icon'    => '✅',
            'color'   => 'success',
            'content_id' => $this->content->id,
        ];
    }
}