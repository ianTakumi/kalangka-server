<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FruitWeight extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fruit_weights';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'harvest_id',
        'weight',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'weight' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Default attribute values.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'local',
    ];

    /**
     * Get the harvest that owns the fruit weight.
     */
    public function harvest()
    {
        return $this->belongsTo(Harvest::class, 'harvest_id', 'id');
    }

    /**
     * Scope a query to only include local market fruits.
     */
    public function scopeLocal($query)
    {
        return $query->where('status', 'local');
    }

    /**
     * Scope a query to only include national market fruits.
     */
    public function scopeNational($query)
    {
        return $query->where('status', 'national');
    }

    /**
     * Check if fruit is for local market.
     */
    public function isLocal(): bool
    {
        return $this->status === 'local';
    }

    /**
     * Check if fruit is for national market.
     */
    public function isNational(): bool
    {
        return $this->status === 'national';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically generate UUID if not provided
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}