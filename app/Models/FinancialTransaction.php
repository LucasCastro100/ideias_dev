<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'account_type_id',
        'category_id',
        'description',
        'value',
        'due_date',
        'paid_date',
        'paid',
        'month',
        'year',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'paid' => 'boolean',
            'due_date' => 'date',
            'paid_date' => 'date',
            'value' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accountType()
    {
        return $this->belongsTo(AccountType::class);
    }

    public function category()
    {
        return $this->belongsTo(FinancialCategory::class, 'category_id');
    }
}
