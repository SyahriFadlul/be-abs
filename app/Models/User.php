<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->profile()->create([
                'name' => $user->name,
                'photo_profile' => 'uploads/user.png'
            ]);
        });
    }

    public function profile() : HasOne
    {

        return $this->hasOne(Profile::class);
    }

    public function cart() : HasMany
    {
        return $this->hasMany(Cart::class);
    }
    
    public function address() : HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function orders() : HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function invoices() : HasMany
    {
        return $this->hasMany(Invoice::class, 'customer_id', 'id');
    }
}
