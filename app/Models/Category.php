<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'tbr_categories';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id', 'slug', 'label', 'modality_level', 'question_level', 'dp_level', 'sort_order',
    ];
}
