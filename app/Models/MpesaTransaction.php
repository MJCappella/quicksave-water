<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpesaTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'mpesa_receipt_no',
        'bank_account_id',
        'type', // Paybill, Till
        'shortcode',
        'phone_number',
        'customer_name',
        'amount',
        'transaction_time',
        'account_reference',
        'status', // Unallocated, Allocated, Reconciled
        'allocated_to_type',
        'allocated_to_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_time' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }
}
