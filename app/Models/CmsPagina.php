<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPagina extends Model
{
    protected $table = 'cms_pages';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'content',
        'published',
    ];

    protected function casts(): array
    {
        return [
            'published' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
