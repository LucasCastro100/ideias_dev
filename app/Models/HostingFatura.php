<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostingFatura extends Model
{
    protected $table = 'hosting_invoices';

    protected $fillable = [
        'user_id',
        'client_id',
        'amount',
        'due_date',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'paid_at' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(HostingCliente::class, 'client_id');
    }
}
