<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MmnPagamento extends Model
{
    protected $table = 'mmn_payments';

    protected $fillable = [
        'user_id',
        'member_id',
        'amount',
        'status',
        'paid_at',
        'proof_note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function member()
    {
        return $this->belongsTo(MmnMembro::class, 'member_id');
    }
}
