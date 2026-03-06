<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'saved_amount',
        'emoji',
        'deadline',
        'description',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'saved_amount'  => 'decimal:2',
        'deadline'      => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->target_amount <= 0) return 0;
        return min(100, round(($this->saved_amount / $this->target_amount) * 100, 1));
    }

    public function getRemainingAttribute()
    {
        return max(0, $this->target_amount - $this->saved_amount);
    }

    public function getIsCompletedAttribute()
    {
        return $this->saved_amount >= $this->target_amount;
    }
}
