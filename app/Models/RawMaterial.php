<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'unit',
        'unit_cost',
        'current_stock',
        'reorder_level',
        'description',
        'is_active',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(MaterialPurchaseItem::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(ProductionMaterialUsage::class);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->reorder_level;
    }
}
