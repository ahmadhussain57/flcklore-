<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Content;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCommentOnContent extends Notification
{
    use Queueable;

    public function __construct(
        public Content $content,
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
            'title'   => '💬 تعليق جديد على محتواك',
            'message' => "علّق {$this->comment->user->name} على «{$this->content->title}»",
            'url'     => route('content.show', $this->content) . '#comments',
            'icon'    => '💬',
            'color'   => 'info',
            'content_id' => $this->content->id,
            'comment_id' => $this->comment->id,
        ];
    }
}