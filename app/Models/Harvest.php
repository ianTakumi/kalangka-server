<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Harvest extends Model
{
    // UUID string, not auto-increment integer
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Fillable fields - with user_id
    protected $fillable = [
        'id',              // Client-generated UUID
        'fruit_id',        // Reference to fruit
        'user_id',         // Reference to user (harvester)
        'ripe_quantity',   // Ripe fruits harvested
        'harvest_at',      // Date of harvest
    ];
    
    // Casts
    protected $casts = [
        'ripe_quantity' => 'integer',
        'harvest_at' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
     * Relationship to User (harvester)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Relationship to FruitWeights
     */
    public function fruitWeights(): HasMany
    {
        return $this->hasMany(FruitWeight::class, 'harvest_id', 'id');
    }
    
    /**
     * Relationship to Wastes
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
        return $query->whereBetween('harvest_at', [$startDate, $endDate]);
    }
    
    /**
     * Scope for specific fruit
     */
    public function scopeForFruit($query, $fruitId)
    {
        return $query->where('fruit_id', $fruitId);
    }
    
    /**
     * Scope for specific user (harvester)
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    /**
     * Scope to get only assigned harvests (have user but not yet harvested)
     */
    public function scopeAssigned($query)
    {
        return $query->whereNotNull('user_id')
                     ->whereNull('harvest_at');
    }
    
    /**
     * Scope to get only completed harvests
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('harvest_at');
    }
    
    /**
     * Scope to get only unassigned harvests (no user assigned)
     */
    public function scopeUnassigned($query)
    {
        return $query->whereNull('user_id');
    }
    
    /**
     * Check if harvest is assigned (has user but not yet harvested)
     */
    public function isAssigned(): bool
    {
        return !is_null($this->user_id) && is_null($this->harvest_at);
    }
    
    /**
     * Check if harvest is completed
     */
    public function isCompleted(): bool
    {
        return !is_null($this->harvest_at);
    }
    
    /**
     * Get total weight from fruit weights
     */
    public function getTotalWeightAttribute()
    {
        return $this->fruitWeights->sum('weight');
    }
    
    /**
     * Get total waste from wastes
     */
    public function getTotalWasteAttribute()
    {
        return $this->wastes->sum('waste_quantity');
    }
    
    /**
     * Get the tree through fruit relationship
     */
    public function tree()
    {
        return $this->hasOneThrough(
            Tree::class,
            Fruit::class,
            'id',        // Foreign key on fruits table
            'id',        // Foreign key on trees table
            'fruit_id',  // Local key on harvests table
            'tree_id'    // Local key on fruits table
        );
    }
}