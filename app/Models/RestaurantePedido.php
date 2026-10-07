<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantePedido extends Model
{
    protected $table = 'mesa_orders';

    protected $fillable = [
        'user_id',
        'table_id',
        'items_summary',
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

    public function table()
    {
        return $this->belongsTo(RestauranteMesa::class, 'table_id');
    }
}
