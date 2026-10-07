<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamModalityScore extends Model
{
    protected $table = 'tbr_team_modality_scores';

    protected $attributes = [
        'scores' => '[]',
    ];

    protected $fillable = [
        'team_id', 'modality_slug', 'round', 'scores', 'total', 'comment',
    ];

    protected function casts(): array
    {
        return [
            'scores' => 'array',
            'total' => 'decimal:2',
        ];
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
