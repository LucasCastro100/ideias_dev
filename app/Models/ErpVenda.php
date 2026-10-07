<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErpVenda extends Model
{
    protected $table = 'erp_sales';

    protected $fillable = [
        'user_id',
        'client_name',
        'total',
        'nfe_number',
        'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'sold_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
