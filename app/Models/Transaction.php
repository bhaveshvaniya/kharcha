<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'description',
        'category',
        'note',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public static function categories()
    {
        return [
            'Food & Dining'    => '🍔',
            'Transport'        => '🚗',
            'Shopping'         => '🛍️',
            'Entertainment'    => '🎬',
            'Healthcare'       => '💊',
            'Bills & Utilities'=> '⚡',
            'Education'        => '📚',
            'Salary'           => '💼',
            'Freelance'        => '💻',
            'Investment'       => '📈',
            'Rent'             => '🏠',
            'Travel'           => '✈️',
            'Other'            => '💰',
        ];
    }
}
