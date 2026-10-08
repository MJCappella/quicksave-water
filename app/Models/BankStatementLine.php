<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankStatementLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_reconciliation_id',
        'line_date',
        'description',
        'reference',
        'withdrawal',
        'deposit',
        'running_balance',
        'is_matched',
        'matched_ref',
    ];

    protected $casts = [
        'line_date' => 'date',
        'withdrawal' => 'decimal:2',
        'deposit' => 'decimal:2',
        'running_balance' => 'decimal:2',
        'is_matched' => 'boolean',
    ];

    public function reconciliation(): BelongsTo
    {
        return $this->belongsTo(BankReconciliation::class, 'bank_reconciliation_id');
    }
}
