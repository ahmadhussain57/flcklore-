<?php

namespace App\Notifications;

use App\Models\Content;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContentPendingEditSubmitted extends Notification
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
            'title'   => '✏️ طلب تعديل على محتوى منشور',
            'message' => "قدّم {$this->content->author->name} تعديلاً على «{$this->content->title}»",
            'url'     => route('review.preview', $this->content),
            'icon'    => '✏️',
            'color'   => 'warning',
            'content_id' => $this->content->id,
        ];
    }
}