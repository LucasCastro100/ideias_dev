<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'category',
        'icon',
        'short_description',
        'long_description',
        'workflow',
        'system_slug',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'workflow' => 'array',
            'active' => 'boolean',
        ];
    }

    public function system()
    {
        return $this->belongsTo(System::class, 'system_slug', 'slug');
    }
}
