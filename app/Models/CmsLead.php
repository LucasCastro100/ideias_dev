<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsLead extends Model
{
    protected $table = 'cms_leads';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'message',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
