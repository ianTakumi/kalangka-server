<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'id', 'title', 'content','topic', 'slug', 
        'featured_image', 'published_at', 'is_published'
    ];
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];
}