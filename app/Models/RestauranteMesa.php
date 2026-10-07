<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestauranteMesa extends Model
{
    protected $table = 'mesa_tables';

    protected $fillable = [
        'user_id',
        'name',
        'seats',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'seats' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(RestaurantePedido::class, 'table_id');
    }
}
