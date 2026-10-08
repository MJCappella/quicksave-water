<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'category',
        'unit_measure',
        'wholesale_price',
        'retail_price',
        'cost_price',
        'reorder_level',
        'is_active',
    ];

    protected $casts = [
        'wholesale_price' => 'decimal:2',
        'retail_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(StoreInventory::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getTotalStockAttribute(): int
    {
        return (int) $this->inventories()->sum('quantity');
    }

    public function isLowStock(): bool
    {
        return $this->total_stock <= $this->reorder_level;
    }
}
