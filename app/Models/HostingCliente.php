<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostingCliente extends Model
{
    protected $table = 'hosting_clients';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(HostingFatura::class, 'client_id');
    }
}
