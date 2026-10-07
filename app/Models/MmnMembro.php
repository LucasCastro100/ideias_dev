<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MmnMembro extends Model
{
    protected $table = 'mmn_members';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'sponsor_id',
        'level',
        'balance',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sponsor()
    {
        return $this->belongsTo(MmnMembro::class, 'sponsor_id');
    }

    public function indicados()
    {
        return $this->hasMany(MmnMembro::class, 'sponsor_id');
    }

    public function downline()
    {
        return $this->hasMany(MmnMembro::class, 'sponsor_id');
    }

    public function payments()
    {
        return $this->hasMany(MmnPagamento::class, 'member_id');
    }
}
