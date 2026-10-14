<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'hetzner_api_token'])]
#[Hidden(['password', 'remember_token', 'hetzner_api_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'hetzner_api_token' => 'encrypted',
        ];
    }

    public function userData(): HasOne
    {
        return $this->hasOne(UserData::class);
    }

    public function contact(): HasOne
    {
        return $this->hasOne(Contact::class);
    }

    /**
     * Determine if the user has connected their Hetzner Cloud account.
     */
    public function hasHetznerApiToken(): bool
    {
        return ! empty($this->hetzner_api_token);
    }

    /**
     * Determine whether the user can manage application users.
     */
    public function isAdmin(): bool
    {
        return in_array(strtolower($this->email), config('users.admin_emails', []), true);
    }
}
