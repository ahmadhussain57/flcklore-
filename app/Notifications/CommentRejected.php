<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentRejected extends Notification
{
    use Queueable;

    protected Comment $comment;
    protected string $reason;

    public function __construct(Comment $comment, string $reason)
    {
        $this->comment = $comment;
        $this->reason  = $reason;
    }

    /**
     * قنوات الإرسال
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * البيانات المحفوظة في قاعدة البيانات
     */
    public function toArray(object $notifiable): array
    {
        $commentable = $this->comment->commentable;
        $isContent   = $this->comment->commentable_type === \App\Models\Content::class;
        $snippet     = mb_strlen($this->comment->body) > 80
            ? mb_substr($this->comment->body, 0, 80) . '…'
            : $this->comment->body;

        // رابط عام (يعود المستخدم للصفحة ليرى تعليقه المرفوض)
        $url = $commentable
            ? ($isContent
                ? route('articles.show', $commentable->slug)
                : route('shop.show', $commentable->slug))
            : '#';

        return [
            'icon'         => '❌',
            'color'        => 'danger',
            'title'        => '❌ تم رفض تعليقك',
            'message'      => $commentable
                ? 'تعليقك على "' . $commentable->title . '" مرفوض. السبب: ' . $this->reason
                : 'تعليقك مرفوض. السبب: ' . $this->reason,
            'url'          => $url,
            'comment_id'   => $this->comment->id,
            'comment_body' => $this->comment->body,
            'reason'       => $this->reason,
            'commentable'  => $commentable ? [
                'id'    => $commentable->id,
                'title' => $commentable->title,
                'slug'  => $commentable->slug,
                'type'  => $isContent ? 'content' : 'product',
            ] : null,
            'reviewed_at' => optional($this->comment->reviewed_at)->toISOString(),
            'created_at'  => now()->toISOString(),
        ];
    }
}