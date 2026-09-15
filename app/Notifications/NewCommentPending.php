<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Content;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCommentPending extends Notification
{
    use Queueable;

    protected Comment $comment;
    protected $commentable;
    protected string $type;

    /**
     * @param Comment $comment       التعليق الجديد
     * @param mixed   $commentable   المحتوى أو المنتج
     * @param string  $type          'content' أو 'product'
     */
    public function __construct(Comment $comment, $commentable, string $type)
    {
        $this->comment     = $comment;
        $this->commentable = $commentable;
        $this->type        = $type;
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
        $isContent = $this->type === 'content';
        $snippet   = mb_strlen($this->comment->body) > 80
            ? mb_substr($this->comment->body, 0, 80) . '…'
            : $this->comment->body;

        // رابط الانتقال: صفحة عرض التعليق في لوحة الإدارة
        $url = route('admin.comments.show', $this->comment);

        return [
            'icon'         => '💬',
            'color'        => 'warning',
            'title'        => $isContent
                ? '💬 تعليق جديد على محتوى'
                : '💬 تعليق جديد على منتج',
            'message'      => 'من ' . $this->comment->user->name . ': "' . $snippet . '"',
            'url'          => $url,
            'comment_id'   => $this->comment->id,
            'comment_body' => $this->comment->body,
            'comment_type' => $this->type,
            'commentable'  => [
                'id'    => $this->commentable->id,
                'title' => $this->commentable->title,
                'slug'  => $this->commentable->slug,
            ],
            'author' => [
                'id'   => $this->comment->user->id,
                'name' => $this->comment->user->name,
            ],
            'created_at' => now()->toISOString(),
        ];
    }
}