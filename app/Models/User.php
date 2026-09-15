<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasSectionRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\RoleUpgradeRequest;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUlids, HasRoles, HasSectionRoles;

    protected $fillable = ['name', 'email', 'password'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roleUpgradeRequests(): HasMany
    {
        return $this->hasMany(RoleUpgradeRequest::class);
    }

    public function contents(): HasMany
    {
        return $this->hasMany(Content::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * ✅ التعليقات التي راجعها المستخدم (كمدقق)
     */
    public function reviewedComments(): HasMany
    {
        return $this->hasMany(Comment::class, 'reviewed_by');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function productReviews(): HasMany
    {
        return $this->hasMany(Product::class, 'reviewed_by');
    }

    // ======================
    // علاقات الشات
    // ======================

    /**
     * المحادثات التي يشارك فيها المستخدم
     */
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->withPivot('last_read_at')
            ->withTimestamps();
    }

    /**
     * سجلات المشاركة (لتفاصيل أكثر)
     */
    public function conversationParticipants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    /**
     * الرسائل التي أرسلها المستخدم
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * عدد الرسائل غير المقروءة (لكل المحادثات)
     */
    public function unreadMessagesCount(): int
    {
        $count = 0;

        foreach ($this->conversations as $conversation) {
            $count += $conversation->unreadCountFor($this);
        }

        return $count;
    }

    // ======================
    // علاقات السلة والطلبات
    // ======================

    /**
     * سلة المستخدم (واحدة فقط)
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * طلبات المستخدم
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }

    /**
     * جلب السلة أو إنشاؤها
     */
    public function getOrCreateCart(): Cart
    {
        return $this->cart ?? Cart::create(['user_id' => $this->id]);
    }

    // ======================
    // علاقات المحاسبة
    // ======================

    /**
     * الفواتير المرتبطة بالمستخدم (كعميل/مورد)
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * الفواتير التي أنشأها المستخدم (كمحاسب)
     */
    public function createdInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'created_by');
    }

    /**
     * سندات القيد التي أنشأها المستخدم (كمحاسب)
     */
    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'created_by');
    }

    /**
     * حركات المواد التي سجلها المستخدم
     */
    public function materialMovements(): HasMany
    {
        return $this->hasMany(MaterialMovement::class, 'created_by');
    }
}