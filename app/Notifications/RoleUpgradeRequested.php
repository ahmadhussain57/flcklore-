<?php

namespace App\Notifications;

use App\Models\RoleUpgradeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RoleUpgradeRequested extends Notification
{
    use Queueable;

    public function __construct(public RoleUpgradeRequest $request)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'   => '⬆️ طلب ترقية جديد',
            'message' => "قدّم {$this->request->user->name} طلباً للترقية إلى دور «{$this->request->requested_role}»",
            'url'     => route('admin.role-upgrade.index'),
            'icon'    => '⬆️',
            'color'   => 'info',
            'request_id' => $this->request->id,
        ];
    }
}