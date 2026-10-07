<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuporteDepartamento extends Model
{
    protected $table = 'suporte_departments';

    protected $fillable = [
        'user_id',
        'name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tickets()
    {
        return $this->hasMany(SuporteTicket::class, 'department_id');
    }
}
