<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $table = 'history';

    protected $fillable = [
        'user_id',
        'product_id',
        'scanned_at',
    ];
    
    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
        ];
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function results() {
        return $this->hasMany(HistoryResult::class);
    }
}
