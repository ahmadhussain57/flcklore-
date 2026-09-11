<?php

namespace App\Notifications;

use App\Models\Content;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContentRejected extends Notification
{
    use Queueable;

    public function __construct(
        public Content $content,
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
            'title'   => '❌ تم رفض محتواك',
            'message' => "تم رفض «{$this->content->title}». السبب: {$this->reason}",
            'url'     => route('content.edit', $this->content),
            'icon'    => '❌',
            'color'   => 'danger',
            'content_id' => $this->content->id,
            'reason'  => $this->reason,
        ];
    }
}