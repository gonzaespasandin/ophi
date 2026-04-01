<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarcodeSuggestionConfirmation extends Model
{
    protected $table = 'barcode_suggestions_confirmations';

    protected $fillable = ['barcode_suggestion_id', 'user_id'];

    public function suggestion() {
        return $this->belongsTo(BarcodeSuggestion::class, 'barcode_suggestion_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
