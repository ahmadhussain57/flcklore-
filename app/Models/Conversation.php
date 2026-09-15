<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasUlids;

    protected $fillable = [
        'created_by',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
        ];
    }

    // ======================
    // العلاقات
    // ======================

    /**
     * من أنشأ المحادثة
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * سجلات المشاركين (مع last_read_at)
     */
    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    /**
     * المستخدمون المشاركون في المحادثة (Many-to-Many)
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    /**
     * كل الرسائل
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    /**
     * آخر رسالة
     */
    public function lastMessage(): ?Message
    {
        return $this->messages()->latest('created_at')->first();
    }

    // ======================
    // دوال مساعدة
    // ======================

    /**
     * هل هذا المستخدم مشارك في المحادثة؟
     */
    public function hasParticipant(User $user): bool
    {
        return $this->users()->where('users.id', $user->id)->exists();
    }

    /**
     * إرجاع الطرف الآخر (في محادثة فردية)
     */
    public function getOtherParticipant(User $currentUser): ?User
    {
        return $this->users()->where('users.id', '!=', $currentUser->id)->first();
    }

    /**
     * هل يوجد رسائل غير مقروءة لهذا المستخدم؟
     */
    public function hasUnreadMessages(User $user): bool
    {
        $participant = $this->participants()->where('user_id', $user->id)->first();

        if (!$participant) {
            return false;
        }

        // إذا لم يقرأ أبداً، هناك رسائل غير مقروءة (إن وُجدت رسائل)
        $query = $this->messages()->where('user_id', '!=', $user->id);

        if ($participant->last_read_at) {
            $query->where('created_at', '>', $participant->last_read_at);
        }

        return $query->exists();
    }

    /**
     * عدد الرسائل غير المقروءة لهذا المستخدم
     */
    public function unreadCountFor(User $user): int
    {
        $participant = $this->participants()->where('user_id', $user->id)->first();

        if (!$participant) {
            return 0;
        }

        $query = $this->messages()->where('user_id', '!=', $user->id);

        if ($participant->last_read_at) {
            $query->where('created_at', '>', $participant->last_read_at);
        }

        return $query->count();
    }
}