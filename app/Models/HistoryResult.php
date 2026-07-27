<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryResult extends Model
{
    public const RESULT_SAFE = 0;
    public const RESULT_WARNING = 1;
    public const RESULT_UNSAFE = 2;
    public const RESULT_UNKNOWN = 3;

    protected $table = 'scan_history_results';

    protected $fillable = [
        'scan_history_id',
        'profile_id',
        'scanned_at',
        'profile_name',
        'profile_avatar',
        'result',
        'unsafe_ingredients',
    ];

    protected $appends = ['is_safe'];

    protected function casts(): array
    {
        return [
            'unsafe_ingredients' => 'array',
            'scanned_at' => 'datetime',
            'result' => 'integer',
        ];
    }

    public $timestamps = false;

    public function history() {
        return $this->belongsTo(History::class, 'scan_history_id');
    }

    public function profile() {
        return $this->belongsTo(Profile::class);
    }

    public function getIsSafeAttribute(): bool
    {
        return (int) $this->result === self::RESULT_SAFE;
    }
}
