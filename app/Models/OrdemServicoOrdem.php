<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdemServicoOrdem extends Model
{
    protected $table = 'os_service_orders';

    protected $fillable = [
        'user_id',
        'customer_id',
        'equipment_description',
        'defect',
        'status',
        'total_value',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'total_value' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(OrdemServicoCliente::class, 'customer_id');
    }

    public function financialEntries()
    {
        return $this->hasMany(OrdemServicoLancamento::class, 'service_order_id');
    }
}
