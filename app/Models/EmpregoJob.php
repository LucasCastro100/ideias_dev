<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpregoJob extends Model
{
    protected $table = 'emprego_jobs';

    protected $fillable = [
        'user_id',
        'company_id',
        'title',
        'category',
        'description',
        'requirements',
        'salary',
        'city',
        'state',
        'expires_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'salary' => 'decimal:2',
            'expires_at' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(EmpregoCompany::class, 'company_id');
    }

    public function applications()
    {
        return $this->hasMany(EmpregoApplication::class, 'job_id');
    }
}
