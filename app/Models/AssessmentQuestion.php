<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    protected $table = 'tbr_assessment_questions';

    protected $fillable = [
        'level', 'modality_slug', 'object_name', 'image', 'mission', 'criteria', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'mission' => 'boolean',
            'criteria' => 'array',
        ];
    }
}
