<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMessageReceived extends Notification
{
    use Queueable;

    public function __construct(
        public Conversation $conversation,
        public Message $message,
        public User $sender
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => "📬 رسالة جديدة من {$this->sender->name}",
            'message' => \Str::limit($this->message->body, 100),
            'url'     => route('messages.show', $this->conversation),
            'icon'    => '📬',
            'color'   => 'info',
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->sender->id,
        ];
    }
}