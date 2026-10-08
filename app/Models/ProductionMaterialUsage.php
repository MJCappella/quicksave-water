<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionMaterialUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_batch_id',
        'raw_material_id',
        'quantity_used',
        'quantity_wasted',
        'unit_cost',
        'total_cost',
    ];

    protected $casts = [
        'quantity_used' => 'decimal:2',
        'quantity_wasted' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductionBatch::class, 'production_batch_id');
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
