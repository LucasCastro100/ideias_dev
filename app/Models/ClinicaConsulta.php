<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicaConsulta extends Model
{
    protected $table = 'clinica_appointments';

    protected $fillable = [
        'user_id',
        'patient_id',
        'doctor_name',
        'appointment_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'appointment_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patient()
    {
        return $this->belongsTo(ClinicaPaciente::class, 'patient_id');
    }
}
