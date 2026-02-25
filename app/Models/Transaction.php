<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'stock_id', 'type', 'quantity', 'price_per_share',
        'total_amount', 'brokerage', 'transaction_date', 'notes'
    ];

    protected $casts = [
        'price_per_share' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'brokerage' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }
}
