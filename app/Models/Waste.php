<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Waste extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wastes';

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
        'waste_quantity',
        'reason',
        'reported_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'waste_quantity' => 'integer',
        'reported_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'reported_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the harvest that owns the waste.
     */
    public function harvest()
    {
        return $this->belongsTo(Harvest::class, 'harvest_id', 'id');
    }

    /**
     * Get the fruit through harvest (optional, if you need direct access)
     */
    public function fruit()
    {
        return $this->hasOneThrough(
            Fruit::class,
            Harvest::class,
            'id', // Foreign key on harvests table
            'id', // Foreign key on fruits table
            'harvest_id', // Local key on wastes table
            'fruit_id' // Local key on harvests table
        );
    }

    /**
     * Scope a query to only include wastes from a specific date range.
     */
    public function scopeReportedBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('reported_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include wastes with a specific reason.
     */
    public function scopeWithReason($query, $reason)
    {
        return $query->where('reason', 'LIKE', "%{$reason}%");
    }

    /**
     * Scope a query to only include wastes with minimum quantity.
     */
    public function scopeMinQuantity($query, $quantity)
    {
        return $query->where('waste_quantity', '>=', $quantity);
    }

    /**
     * Get the reason in title case.
     */
    public function getFormattedReasonAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->reason));
    }

    /**
     * Check if waste quantity is high (more than 5).
     */
    public function isHighWaste(): bool
    {
        return $this->waste_quantity > 5;
    }

    /**
     * Get waste statistics for a harvest.
     */
    public static function getHarvestWasteStats(string $harvestId): array
    {
        $totalWaste = self::where('harvest_id', $harvestId)->sum('waste_quantity');
        $wasteCount = self::where('harvest_id', $harvestId)->count();
        $commonReason = self::where('harvest_id', $harvestId)
            ->select('reason', \DB::raw('count(*) as total'))
            ->groupBy('reason')
            ->orderByDesc('total')
            ->first();

        return [
            'total_waste' => $totalWaste,
            'waste_entries' => $wasteCount,
            'most_common_reason' => $commonReason?->reason ?? 'N/A',
        ];
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
            
            // Set reported_at if not provided
            if (empty($model->reported_at)) {
                $model->reported_at = now();
            }
        });

        // Update timestamps on updating
        static::updating(function ($model) {
            $model->updated_at = now();
        });
    }
}