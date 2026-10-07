<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailMktLista extends Model
{
    protected $table = 'email_lists';

    protected $fillable = [
        'user_id',
        'name',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscribers()
    {
        return $this->hasMany(EmailMktAssinante::class, 'list_id');
    }

    public function campaigns()
    {
        return $this->hasMany(EmailMktCampanha::class, 'list_id');
    }
}
