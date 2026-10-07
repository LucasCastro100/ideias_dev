<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientPlan extends Model
{
    protected $table = 'client_plan';

    protected $fillable = [
        'user_id',
        'client_id',
        'plan_id',
        'start_date',
        'end_date',
        'cancelled_at',
        'active',
        'adjusted_value',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
            'cancelled_at' => 'date',
            'adjusted_value' => 'decimal:2',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
