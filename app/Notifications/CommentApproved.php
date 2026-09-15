<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentApproved extends Notification
{
    use Queueable;

    protected Comment $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
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
        $commentable    = $this->comment->commentable;
        $isContent      = $this->comment->commentable_type === \App\Models\Content::class;
        $snippet        = mb_strlen($this->comment->body) > 80
            ? mb_substr($this->comment->body, 0, 80) . '…'
            : $this->comment->body;

        // رابط التعليق في الصفحة العامة
        $url = $commentable
            ? ($isContent
                ? route('articles.show', $commentable->slug) . '#comment-' . $this->comment->id
                : route('shop.show', $commentable->slug) . '#comment-' . $this->comment->id)
            : '#';

        return [
            'icon'         => '✅',
            'color'        => 'success',
            'title'        => '✅ تم نشر تعليقك',
            'message'      => $commentable
                ? 'تمت الموافقة على تعليقك على "' . $commentable->title . '": "' . $snippet . '"'
                : 'تمت الموافقة على تعليقك.',
            'url'          => $url,
            'comment_id'   => $this->comment->id,
            'comment_body' => $this->comment->body,
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