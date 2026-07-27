<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
        'body',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_notification')
            ->withPivot(['is_read', 'is_archived']);
    }
}
