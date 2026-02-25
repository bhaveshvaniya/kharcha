<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Stock extends Model
{
    protected $fillable = [
        'symbol', 'company_name', 'sector', 'exchange',
        'current_price', 'previous_close', 'is_active'
    ];

    protected $casts = [
        'current_price' => 'decimal:2',
        'previous_close' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function prices(): HasMany
    {
        return $this->hasMany(StockPrice::class);
    }

    public function todayPrice(): HasOne
    {
        return $this->hasOne(StockPrice::class)->where('price_date', today())->latest();
    }

    public function latestPrice(): HasOne
    {
        return $this->hasOne(StockPrice::class)->latestOfMany('price_date');
    }

    public function holding(): HasOne
    {
        return $this->hasOne(PortfolioHolding::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getChangeAmountAttribute(): float
    {
        return $this->current_price - $this->previous_close;
    }

    public function getChangePercentAttribute(): float
    {
        if ($this->previous_close == 0) return 0;
        return (($this->current_price - $this->previous_close) / $this->previous_close) * 100;
    }
}
