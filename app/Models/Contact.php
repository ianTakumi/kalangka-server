<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    // Specify the primary key type
    protected $keyType = 'string';
    public $incrementing = false;

    // Specify which fields can be mass assigned
    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
    ];

    // Cast attributes to specific types
    protected $casts = [
        'id' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Define constants for status
    const STATUS_NEW = 'new';
    const STATUS_READ = 'read';
    const STATUS_RESOLVED = 'resolved';

    // Helper methods
    public function markAsRead()
    {
        $this->update(['status' => self::STATUS_READ]);
    }

    public function markAsResolved()
    {
        $this->update(['status' => self::STATUS_RESOLVED]);
    }

    public function isNew()
    {
        return $this->status === self::STATUS_NEW;
    }

    public function isRead()
    {
        return $this->status === self::STATUS_READ;
    }

    public function isResolved()
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    // Scopes for filtering
    public function scopeUnread($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }
}