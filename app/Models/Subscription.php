<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'plan_id',
        'payment_id',
        'subscription_start_timestamp',
        'subscription_end_timestamp',
        'auto_renovate',
    ];

    protected $appends = [
        'amount',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'subscription_start_timestamp' => 'datetime',
            'subscription_end_timestamp' => 'datetime',
            'auto_renovate' => 'boolean',
        ];
    }

    public function plan() {
        return $this->belongsTo(Plan::class);
    }

    public function payment() {
        return $this->belongsTo(Payment::class);
    }

    public function getAmountAttribute(): float
    {
        if ($this->relationLoaded('payment') && $this->payment) {
            return (float) $this->payment->amount;
        }

        if ($this->payment_id) {
            return (float) ($this->payment()->value('amount') ?? 0);
        }

        return (float) ($this->plan?->price_per_month ?? 0);
    }

    public function getExpiresAtAttribute()
    {
        return $this->subscription_end_timestamp;
    }
}
