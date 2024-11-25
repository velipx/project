<?php

namespace App\Models;

use Bavix\Wallet\Traits\HasWalletFloat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Filterable;
use Laravel\Sanctum\HasApiTokens;
use Bavix\Wallet\Traits\HasWallets;
use Bavix\Wallet\Interfaces\Wallet;

class User extends Authenticatable implements Wallet
{
    use HasApiTokens, HasRoles, HasFactory, Notifiable, SoftDeletes, Filterable, HasWallets, HasWalletFloat;

    protected $fillable = [
        'username',
        'name',
        'email',
        'avatar_url',
        'password',
        'deleted_at',
        'google2fa_secret',
        'google2fa_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'google2fa_enabled' => 'boolean',
        ];
    }

    public function sessions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\Illuminate\Database\Eloquent\Model::class, 'user_id', 'id');
    }

    public function passwordChangeLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PasswordChangeLog::class, 'user_id', 'id');
    }

    // Method to get the default address of a given wallet slug
    public function getDefaultAddress($slug)
    {
        $wallet = $this->getWallet($slug); // Getting the wallet using laravel-wallet method

        if ($wallet) {
            return $wallet->defaultAddress()->first();
        }

        return null; // Or handle as needed
    }
}
