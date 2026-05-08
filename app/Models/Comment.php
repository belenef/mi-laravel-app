<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['content', 'user_id', 'playlist_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }
}
