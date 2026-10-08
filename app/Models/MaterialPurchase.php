<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_no',
        'supplier_name',
        'supplier_invoice_no',
        'purchase_date',
        'status',
        'total_amount',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MaterialPurchaseItem::class);
    }
}
