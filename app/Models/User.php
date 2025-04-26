<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'has_subscription',
        'telephone',
        'adresse'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'has_subscription' => 'boolean',
        'email_verified_at' => 'datetime'
    ];

    /**
     * Get the subscriptions for the user.
     */
    public function abonnements(): HasMany
    {
        return $this->hasMany(Abonnement::class);
    }

    /**
     * Get the user's subscription status.
     *
     * @return bool
     */
    public function getHasSubscriptionAttribute($value)
    {
        return (bool) $value;
    }

    /**
     * Set the user's subscription status.
     *
     * @param bool $value
     * @return void
     */
    public function setHasSubscriptionAttribute($value)
    {
        $this->attributes['has_subscription'] = (bool) $value;
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }
}
