<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fruit extends Model
{
    // Since we're using UUID string, not auto-increment integer
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Fillable fields
    protected $fillable = [
        'id',           // Client-generated UUID (from React Native)
        'flower_id',    // Reference to flower
        'tree_id',      // Reference to tree
        'quantity',     // Number of fruits
        'wrappted_at',  // Note: Double 'p' to match your SQL
        'image_url',    // Image URL
    ];
    
    // Casts
    protected $casts = [
        'quantity' => 'integer',
        'wrappted_at' => 'datetime',
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
}