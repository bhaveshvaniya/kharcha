<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioHolding extends Model
{
    protected $fillable = [
        'stock_id', 'quantity', 'average_buy_price',
        'total_invested', 'first_purchase_date', 'notes'
    ];

    protected $casts = [
        'average_buy_price' => 'decimal:2',
        'total_invested' => 'decimal:2',
        'first_purchase_date' => 'date',
    ];

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function getCurrentValueAttribute(): float
    {
        return $this->quantity * $this->stock->current_price;
    }

    public function getProfitLossAttribute(): float
    {
        return $this->current_value - $this->total_invested;
    }

    public function getProfitLossPercentAttribute(): float
    {
        if ($this->total_invested == 0) return 0;
        return ($this->profit_loss / $this->total_invested) * 100;
    }
}
