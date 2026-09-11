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

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable,HasUlids ,HasRoles,HasSectionRoles;
    protected $fillable = ['name','email','password'];
    
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


}
