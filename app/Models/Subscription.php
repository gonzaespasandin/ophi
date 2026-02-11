<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'user_has_plan';
    protected $fillable = ['user_id', 'plan_id', 'amount', 'expires_at'];

    public function plan() {
        return $this->belongsTo(Plan::class);
    }
}
