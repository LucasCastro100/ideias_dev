<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'team_id',
        'name',
        'description',
        'value',
        'billing_cycle',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'active' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Company::class, 'team_id');
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_plan')
            ->using(ClientPlan::class)
            ->withPivot(['start_date', 'end_date', 'active', 'adjusted_value', 'user_id'])
            ->withTimestamps();
    }
}
