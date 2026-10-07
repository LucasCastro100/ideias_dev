<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modality extends Model
{
    protected $table = 'tbr_modalities';

    protected $fillable = [
        'level', 'config_id', 'slug', 'label', 'sort_order',
    ];
}
