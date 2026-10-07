<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'team_id',
        'name',
        'email',
        'phone',
        'document',
        'cep',
        'sexo',
        'birth_date',
        'endereco',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'notes',
        'active',
        'deactivated_at',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'birth_date' => 'date',
            'deactivated_at' => 'datetime',
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

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'client_plan')
            ->using(ClientPlan::class)
            ->withPivot(['start_date', 'end_date', 'active', 'adjusted_value', 'user_id'])
            ->withTimestamps();
    }

    public function activePlans()
    {
        return $this->belongsToMany(Plan::class, 'client_plan')
            ->using(ClientPlan::class)
            ->withPivot(['start_date', 'end_date', 'active', 'adjusted_value', 'user_id'])
            ->withTimestamps()
            ->wherePivot('active', true);
    }

    public function accounts()
    {
        return $this->hasMany(ClientAccount::class);
    }
}
