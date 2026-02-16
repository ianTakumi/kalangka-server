<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flower extends Model
{
    // Disable auto-incrementing ID since we're using string UUID
    public $incrementing = false;
    
    // Set key type to string
    protected $keyType = 'string';
    
    // Fillable fields
    protected $fillable = [
        'id', // Client-generated UUID from React Native
        'tree_id',
        'quantity',
        'wrapped_at',
        'image_url',
    ];
    
    // Casts
    protected $casts = [
        'wrapped_at' => 'datetime',
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Relationship to Tree model
    public function tree()
    {
        return $this->belongsTo(Tree::class, 'tree_id');
    }
    
    // Optional: Auto-generate UUID if not provided by client
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}