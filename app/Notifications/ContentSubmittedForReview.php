<?php

namespace App\Notifications;

use App\Models\Content;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ContentSubmittedForReview extends Notification
{
    use Queueable;

    /**
     * @param Content $content المحتوى المُقدَّم للمراجعة
     */
    public function __construct(public Content $content)
    {
    }

    /**
     * القنوات التي سيُرسل عبرها الإشعار
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * البيانات التي ستُخزَّن في جدول notifications
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => '📝 محتوى جديد بانتظار المراجعة',
            'message' => "قدّم {$this->content->author->name} محتوى بعنوان: «{$this->content->title}»",
            'url'     => route('review.preview', $this->content),
            'icon'    => '📝',
            'color'   => 'warning',
            'content_id' => $this->content->id,
        ];
    }
}