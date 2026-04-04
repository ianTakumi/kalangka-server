<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fruit extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Fillable fields
    protected $fillable = [
        'id',         
        'flower_id',   
        'tree_id',     
        'user_id',
        'quantity',   
        'tag_id',
        'bagged_at',  
        'image_url',    
    ];
    
    // Casts
    protected $casts = [
        'quantity' => 'integer',
        'bagged_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    /**
     * Auto-generate UUID if not provided by client
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
    
    /**
     * Relationship to Flower
     */
    public function flower(): BelongsTo
    {
        return $this->belongsTo(Flower::class, 'flower_id');
    }
    
    /**
     * Relationship to Tree
     */
    public function tree(): BelongsTo
    {
        return $this->belongsTo(Tree::class, 'tree_id');
    }

    /**
     * Relationship to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}