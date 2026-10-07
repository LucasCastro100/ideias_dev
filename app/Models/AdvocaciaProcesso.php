<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvocaciaProcesso extends Model
{
    protected $table = 'advocacia_cases';

    protected $fillable = [
        'user_id',
        'client_id',
        'title',
        'case_no',
        'court',
        'stage',
        'hearing_date',
        'fees',
    ];

    protected function casts(): array
    {
        return [
            'hearing_date' => 'date',
            'fees' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(AdvocaciaCliente::class, 'client_id');
    }
}
