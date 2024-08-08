<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'first_name',
        'last_name',
        'phone_number',
        'email_verified_at',
        'password',
        'is_active',
        'last_active_at',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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


    public function images()
    {
        return $this->hasMany(UserImage::class);
    }

    public function fcmTokens()
    {
        return $this->hasMany(UserFcmToken::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
    public function orders(){
        return $this->hasMany(UserOrder::class, 'user_id');
    }
    public function cart_products(){
        return $this->belongsToMany(Product::class, 'cart_items', 'user_id', 'product_id');
    }
}
