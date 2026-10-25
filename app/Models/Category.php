<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'color', 'description'];

    public function keywords(): HasMany
    {
        return $this->hasMany(Keyword::class);
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)->withPivot('score', 'matched', 'is_primary');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }
}
