<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryResult extends Model
{
    protected $table = 'history_results';

    protected $fillable = [
        'history_id',
        'profile_id',
        'is_safe',
        'unsafe_ingredients',
    ];

    protected function casts(): array
    {
        return [
            'unsafe_ingredients' => 'array',
            'is_safe' => 'boolean',
        ];
    }

    public $timestamps = false;

    public function history() {
        return $this->belongsTo(History::class);
    }

    public function profile() {
        return $this->belongsTo(Profile::class);
    }
}
