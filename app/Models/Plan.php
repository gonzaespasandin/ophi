<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'price_per_month',
        'price_per_year',
    ];

    protected $appends = [
        'plan',
        'price',
        'duration',
    ];

    protected function casts(): array
    {
        return [
            'price_per_month' => 'decimal:2',
            'price_per_year' => 'decimal:2',
        ];
    }

    public function getPlanAttribute(): ?string
    {
        return $this->name;
    }

    public function getPriceAttribute(): float
    {
        return (float) $this->price_per_month;
    }

    public function getDurationAttribute(): int
    {
        return $this->name === 'premium' ? 30 : 0;
    }
}
