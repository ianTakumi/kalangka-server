<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;  // <- ADD THIS

class Harvest extends Model
{
    // UUID string, not auto-increment integer
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Fillable fields
    protected $fillable = [
        'id',              // Client-generated UUID
        'fruit_id',        // Reference to fruit
        'ripe_quantity',   // Ripe fruits harvested
        'harvest_at',      // Date of harvest
    ];
    
    // Casts
    protected $casts = [
        'ripe_quantity' => 'integer',
        'harvest_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    /**
     * Auto-generate UUID if not provided
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
     * Relationship to Fruit
     */
    public function fruit(): BelongsTo
    {
        return $this->belongsTo(Fruit::class, 'fruit_id');
    }
    
    /**
     * Relationship to FruitWeights - ADD THIS!
     */
    public function fruitWeights(): HasMany
    {
        return $this->hasMany(FruitWeight::class, 'harvest_id', 'id');
    }
    
    /**
     * Relationship to Wastes - ADD THIS!
     */
    public function wastes(): HasMany
    {
        return $this->hasMany(Waste::class, 'harvest_id', 'id');
    }
    
    /**
     * Scope for harvesting date range
     */
    public function scopeHarvestedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('harvest_at', [$startDate, $endDate]); // <- Fixed to harvest_at
    }
    
    /**
     * Scope for specific fruit
     */
    public function scopeForFruit($query, $fruitId)
    {
        return $query->where('fruit_id', $fruitId);
    }
}