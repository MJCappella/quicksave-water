<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'txn_no',
        'bank_account_id',
        'txn_date',
        'type', // INFLOW, OUTFLOW, CONTRA
        'category',
        'amount',
        'payee_or_payer',
        'reference_no',
        'description',
        'approved_by',
        'is_reconciled',
    ];

    protected $casts = [
        'txn_date' => 'date',
        'amount' => 'decimal:2',
        'is_reconciled' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }
}
