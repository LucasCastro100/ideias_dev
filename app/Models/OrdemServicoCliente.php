<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdemServicoCliente extends Model
{
    protected $table = 'os_customers';

    protected $fillable = [
        'user_id',
        'name',
        'document',
        'phone',
        'email',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceOrders()
    {
        return $this->hasMany(OrdemServicoOrdem::class, 'customer_id');
    }
}
