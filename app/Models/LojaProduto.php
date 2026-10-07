<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LojaProduto extends Model
{
    protected $table = 'loja_products';

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'price',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
