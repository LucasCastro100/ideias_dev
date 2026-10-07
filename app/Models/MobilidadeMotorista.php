<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobilidadeMotorista extends Model
{
    protected $table = 'mobi_drivers';

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'license_no',
        'vehicle_category',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rides()
    {
        return $this->hasMany(MobilidadeCorrida::class, 'driver_id');
    }
}
