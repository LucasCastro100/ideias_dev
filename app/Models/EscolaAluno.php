<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscolaAluno extends Model
{
    protected $table = 'escola_students';

    protected $fillable = [
        'user_id',
        'class_id',
        'name',
        'birth_date',
        'guardian_name',
        'guardian_phone',
        'email',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'active' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function turma()
    {
        return $this->belongsTo(EscolaTurma::class, 'class_id');
    }

    public function invoices()
    {
        return $this->hasMany(EscolaFatura::class, 'student_id');
    }
}
