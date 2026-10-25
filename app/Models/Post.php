<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    protected $fillable = [
        'source_id', 'user_id', 'title', 'body', 'region', 'published_at',
        'sentiment_score', 'sentiment', 'severity_score', 'severity', 'top_words', 'status',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'top_words' => 'array',
        'sentiment_score' => 'float',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withPivot('score', 'matched', 'is_primary');
    }

    /** Ең жоғары баллы бар (негізгі) санат. */
    public function primary(): ?Category
    {
        return $this->categories->sortByDesc('pivot.score')->first();
    }
}
