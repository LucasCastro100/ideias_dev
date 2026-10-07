<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuporteTicket extends Model
{
    protected $table = 'suporte_tickets';

    protected $fillable = [
        'user_id',
        'department_id',
        'subject',
        'message',
        'priority',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(SuporteDepartamento::class, 'department_id');
    }
}
