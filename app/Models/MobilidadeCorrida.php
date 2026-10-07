<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobilidadeCorrida extends Model
{
    protected $table = 'mobi_rides';

    protected $fillable = [
        'user_id',
        'driver_id',
        'rider_name',
        'pickup_address',
        'drop_address',
        'status',
        'distance_km',
        'amount',
        'requested_at',
    ];

    protected function casts(): array
    {
        return [
            'distance_km' => 'decimal:2',
            'amount' => 'decimal:2',
            'requested_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(MobilidadeMotorista::class, 'driver_id');
    }
}
