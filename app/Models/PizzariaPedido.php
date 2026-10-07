<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PizzariaPedido extends Model
{
    protected $table = 'pizza_orders';

    protected $fillable = [
        'user_id',
        'customer_name',
        'delivery_address',
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
