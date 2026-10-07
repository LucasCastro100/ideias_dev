<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmpregoCompany extends Model
{
    protected $table = 'emprego_companies';

    protected $fillable = [
        'user_id',
        'name',
        'contact_name',
        'email',
        'phone',
        'city',
        'state',
        'description',
        'plan',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobs()
    {
        return $this->hasMany(EmpregoJob::class, 'company_id');
    }
}
