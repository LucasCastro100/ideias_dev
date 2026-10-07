<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdemServicoLancamento extends Model
{
    protected $table = 'os_financial_entries';

    protected $fillable = [
        'user_id',
        'service_order_id',
        'description',
        'amount',
        'due_date',
        'paid',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'paid' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceOrder()
    {
        return $this->belongsTo(OrdemServicoOrdem::class, 'service_order_id');
    }
}
