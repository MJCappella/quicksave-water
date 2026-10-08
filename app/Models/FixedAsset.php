<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code',
        'name',
        'category',
        'serial_no',
        'purchase_date',
        'purchase_cost',
        'salvage_value',
        'useful_life_years',
        'depreciation_method',
        'annual_depreciation_rate',
        'accumulated_depreciation',
        'current_book_value',
        'location',
        'assigned_to',
        'status',
        'last_maintenance_date',
        'next_maintenance_date',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'annual_depreciation_rate' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'current_book_value' => 'decimal:2',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
    ];

    /**
     * Calculate updated depreciation based on age.
     */
    public function calculateDepreciation(): array
    {
        $yearsElapsed = max(0, now()->diffInDays($this->purchase_date) / 365.25);
        $depreciableBase = max(0, $this->purchase_cost - $this->salvage_value);

        if ($this->depreciation_method === 'Straight-Line') {
            $annualDepr = $this->useful_life_years > 0 ? $depreciableBase / $this->useful_life_years : 0;
            $accum = min($depreciableBase, $annualDepr * $yearsElapsed);
        } else {
            // Reducing balance
            $rate = $this->annual_depreciation_rate / 100;
            $current = $this->purchase_cost;
            for ($i = 0; $i < floor($yearsElapsed); $i++) {
                $current *= (1 - $rate);
            }
            $accum = max(0, $this->purchase_cost - $current);
        }

        $bookValue = max($this->salvage_value, $this->purchase_cost - $accum);

        return [
            'accumulated' => round($accum, 2),
            'book_value' => round($bookValue, 2),
        ];
    }
}
