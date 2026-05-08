<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'category', 'description', 'members_count', 'color', 'icon', 'user_id'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
