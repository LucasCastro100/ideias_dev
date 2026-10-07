<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvocaciaCliente extends Model
{
    protected $table = 'advocacia_clients';

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cases()
    {
        return $this->hasMany(AdvocaciaProcesso::class, 'client_id');
    }
}
