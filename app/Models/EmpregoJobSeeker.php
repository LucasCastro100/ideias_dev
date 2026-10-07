<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpregoJobSeeker extends Model
{
    protected $table = 'emprego_job_seekers';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'city',
        'state',
        'birth_date',
        'summary',
        'skills',
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

    public function applications()
    {
        return $this->hasMany(EmpregoApplication::class, 'job_seeker_id');
    }
}
