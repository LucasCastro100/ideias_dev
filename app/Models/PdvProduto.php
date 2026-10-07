<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdvProduto extends Model
{
    protected $table = 'pdv_products';

    protected $fillable = [
        'user_id',
        'name',
        'code',
        'price',
        'cost',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
