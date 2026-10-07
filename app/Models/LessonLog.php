<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonLog extends Model
{
    protected $fillable = [
        'user_id',
        'team_id',
        'client_id',
        'lesson_date',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'lesson_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Company::class, 'team_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
