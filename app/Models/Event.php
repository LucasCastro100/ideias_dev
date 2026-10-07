<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $table = 'tbr_events';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'date', 'status', 'tipo_evento', 'location', 'ranking_config',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'status' => 'boolean',
            'location' => 'array',
            'ranking_config' => 'array',
        ];
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
