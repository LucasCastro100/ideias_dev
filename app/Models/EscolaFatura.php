<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscolaFatura extends Model
{
    protected $table = 'escola_invoices';

    protected $fillable = [
        'user_id',
        'student_id',
        'title',
        'amount',
        'due_date',
        'paid',
        'paid_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'paid' => 'boolean',
            'paid_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function student()
    {
        return $this->belongsTo(EscolaAluno::class, 'student_id');
    }
}
