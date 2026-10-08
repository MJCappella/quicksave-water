<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankReconciliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reconciliation_no',
        'bank_account_id',
        'statement_date',
        'statement_ending_balance',
        'book_balance',
        'cleared_balance',
        'difference',
        'status',
        'reconciled_by',
        'notes',
    ];

    protected $casts = [
        'statement_date' => 'date',
        'statement_ending_balance' => 'decimal:2',
        'book_balance' => 'decimal:2',
        'cleared_balance' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(BankStatementLine::class);
    }
}
