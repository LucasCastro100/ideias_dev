<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdvVenda extends Model
{
    protected $table = 'pdv_sales';

    protected $fillable = [
        'user_id',
        'customer_name',
        'total',
        'discount',
        'status',
        'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'discount' => 'decimal:2',
            'sold_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
