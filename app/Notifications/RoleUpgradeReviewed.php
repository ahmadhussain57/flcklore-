<?php

namespace App\Notifications;

use App\Models\RoleUpgradeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RoleUpgradeReviewed extends Notification
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
        $approved = $this->request->status === 'approved';

        return [
            'title'   => $approved ? '✅ تمت الموافقة على طلب الترقية' : '❌ تم رفض طلب الترقية',
            'message' => $approved
                ? "تمت ترقيتك إلى دور «{$this->request->requested_role}» بنجاح!"
                : "تم رفض طلب ترقيتك إلى «{$this->request->requested_role}».",
            'url'     => route('dashboard'),
            'icon'    => $approved ? '✅' : '❌',
            'color'   => $approved ? 'success' : 'danger',
            'request_id' => $this->request->id,
        ];
    }
}