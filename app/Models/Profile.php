<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'avatar',
        'avatar_color',
        'user_id',
        'owner_id',
        'share_token',
        'is_main',
    ];

    protected function casts(): array
    {
        return [
            'is_main' => 'boolean',
        ];
    }

    public function user() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function owner() {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function sharedUsers() {
        return $this->belongsToMany(User::class, 'profile_user')
            ->withPivot('can_edit');
    }

    public function ingredients() {
        return $this->belongsToMany(
            Ingredient::class,
            'ingredient_profile',
            'profile_id',
            'ingredient_id'
        )->withPivot('care');
    }
}
