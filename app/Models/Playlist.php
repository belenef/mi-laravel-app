<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $fillable = ['title', 'description', 'mood', 'cover', 'is_collaborative', 'user_id', 'songs'];

    protected $casts = [
        'is_collaborative' => 'boolean',
        'songs' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
