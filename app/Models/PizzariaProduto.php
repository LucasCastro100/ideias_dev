<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PizzariaProduto extends Model
{
    protected $table = 'pizza_products';

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
