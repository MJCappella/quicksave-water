<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreTransferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_transfer_id',
        'product_id',
        'quantity_sent',
        'quantity_received',
        'notes',
    ];

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(StoreTransfer::class, 'store_transfer_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
