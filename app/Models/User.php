<?php

namespace App\Models;

use App\Notifications\Verificationurl;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function products()
    {
        return $this->hasMany(Products::class, 'owner');
    }
    public function verificationNotification()
    {
        $key = $this->id . '_email_verification_code';
        $value = uniqid();
        Cache::store('database')->put($key, $value, now()->addMinutes(30));
        $url = route('verification.verify', ['user' => $this->id, 'code' => $value ]);
        $this->notify(new Verificationurl($url));
    }
}
