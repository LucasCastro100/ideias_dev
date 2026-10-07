<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicaPaciente extends Model
{
    protected $table = 'clinica_patients';

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'birth_date',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(ClinicaConsulta::class, 'patient_id');
    }
}
