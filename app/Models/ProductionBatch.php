<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_no',
        'product_id',
        'store_id',
        'planned_quantity',
        'actual_yield',
        'rejected_quantity',
        'batch_date',
        'status',
        'production_cost',
        'supervisor_name',
        'shift',
        'water_source_reading',
        'notes',
    ];

    protected $casts = [
        'batch_date' => 'date',
        'planned_quantity' => 'integer',
        'actual_yield' => 'integer',
        'rejected_quantity' => 'integer',
        'production_cost' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(ProductionMaterialUsage::class);
    }

    public function getEfficiencyRateAttribute(): float
    {
        if ($this->planned_quantity <= 0) {
            return 100.0;
        }

        return round(($this->actual_yield / $this->planned_quantity) * 100, 1);
    }
}
