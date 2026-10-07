<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpregoApplication extends Model
{
    protected $table = 'emprego_applications';

    protected $fillable = [
        'user_id',
        'job_id',
        'job_seeker_id',
        'status',
        'applied_at',
    ];

    protected function casts(): array
    {
        return [
            'applied_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function job()
    {
        return $this->belongsTo(EmpregoJob::class, 'job_id');
    }

    public function jobSeeker()
    {
        return $this->belongsTo(EmpregoJobSeeker::class, 'job_seeker_id');
    }
}
