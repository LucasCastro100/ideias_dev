<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EscolaTurma extends Model
{
    protected $table = 'escola_classes';

    protected $fillable = [
        'user_id',
        'name',
        'teacher_name',
        'shift',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->hasMany(EscolaAluno::class, 'class_id');
    }
}
