<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LojaPedido extends Model
{
    protected $table = 'loja_orders';

    protected $fillable = [
        'user_id',
        'customer_name',
        'total',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
