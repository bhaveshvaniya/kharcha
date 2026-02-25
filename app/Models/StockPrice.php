<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockPrice extends Model
{
    protected $fillable = [
        'stock_id', 'price_date', 'open_price', 'high_price',
        'low_price', 'close_price', 'volume', 'change_amount', 'change_percent'
    ];

    protected $casts = [
        'price_date' => 'date',
        'open_price' => 'decimal:2',
        'high_price' => 'decimal:2',
        'low_price' => 'decimal:2',
        'close_price' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'change_percent' => 'decimal:4',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }
}
