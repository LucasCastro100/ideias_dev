<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'tbr_teams';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'event_id', 'name', 'category_slug', 'total_score',
        'representative_name', 'representative_email', 'representative_phone',
    ];

    protected function casts(): array
    {
        return [
            'total_score' => 'decimal:2',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function scores()
    {
        return $this->hasMany(TeamModalityScore::class);
    }
}
