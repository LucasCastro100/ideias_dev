<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErpProduto extends Model
{
    protected $table = 'erp_products';

    protected $fillable = [
        'user_id',
        'name',
        'cost',
        'price',
        'stock',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
