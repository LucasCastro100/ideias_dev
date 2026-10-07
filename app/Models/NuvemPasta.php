<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NuvemPasta extends Model
{
    protected $table = 'nuvem_folders';

    protected $fillable = [
        'user_id',
        'name',
        'parent_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(NuvemPasta::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(NuvemPasta::class, 'parent_id');
    }

    public function files()
    {
        return $this->hasMany(NuvemArquivo::class, 'folder_id');
    }
}
