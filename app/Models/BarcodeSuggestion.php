<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarcodeSuggestion extends Model
{
    protected $fillable = ['barcode', 'product_id', 'suggested_by_user_id', 'status'];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function suggestedBy() {
        return $this->belongsTo(User::class, 'suggested_by_user_id');
    }

    public function confirmations() {
        return $this->hasMany(BarcodeSuggestionConfirmation::class);
    }
}
