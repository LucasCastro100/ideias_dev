<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientAccount extends Model
{
    protected $fillable = [
        'user_id',
        'team_id',
        'client_id',
        'plan_id',
        'description',
        'value',
        'due_date',
        'paid_date',
        'paid',
        'category',
        'notes',
        'month',
        'year',
        'cancelled_at',
        'recurring_group_id',
        'recurring_until_month',
        'recurring_until_year',
    ];

    protected function casts(): array
    {
        return [
            'paid' => 'boolean',
            'due_date' => 'date',
            'paid_date' => 'date',
            'cancelled_at' => 'datetime',
            'recurring_until_month' => 'integer',
            'recurring_until_year' => 'integer',
            'value' => 'decimal:2',
        ];
    }

    public function scopeActive($query)
    {
        return $query->whereNull('cancelled_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('cancelled_at')->where('paid', false);
    }

    public function scopePaid($query)
    {
        return $query->whereNull('cancelled_at')->where('paid', true);
    }

    public function scopeRecurring($query)
    {
        return $query->whereNotNull('recurring_group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Company::class, 'team_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function recurringGroup()
    {
        return $this->hasMany(ClientAccount::class, 'recurring_group_id', 'recurring_group_id');
    }
}
