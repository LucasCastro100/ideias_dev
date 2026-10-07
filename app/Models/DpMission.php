<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DpMission extends Model
{
    protected $table = 'tbr_dp_missions';

    protected $fillable = [
        'year', 'dp_level', 'mission_title', 'description', 'image', 'items', 'depends_on', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
        ];
    }
}
