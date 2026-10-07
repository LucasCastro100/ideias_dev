<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialGrupo extends Model
{
    protected $table = 'social_groups';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'privacy',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
