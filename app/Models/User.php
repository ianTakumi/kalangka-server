<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',           // ← ADD THIS para sa UUID
        'first_name', 
        'last_name',   
        'gender',
        'email',
        'role',         // ← ADD THIS para sa role
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'id' => 'string',  // ← OPTIONAL: Ensure ID is treated as string
        ];
    }

    /**
     * Disable auto-incrementing since we're using UUID
     */
    public $incrementing = false;  // ← ADD THIS

    /**
     * Set the key type to string
     */
    protected $keyType = 'string';  // ← ADD THIS

    /**
     * Accessor for full name
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}