<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'requisition_no',
        'department',
        'requested_by',
        'requisition_date',
        'required_date',
        'estimated_total',
        'priority',
        'status',
        'purpose',
    ];

    protected $casts = [
        'requisition_date' => 'date',
        'required_date' => 'date',
        'estimated_total' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseRequisitionItem::class);
    }
}
