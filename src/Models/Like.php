<?php

namespace Turahe\Likeable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Turahe\Likeable\Contracts\Like as LikeContract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Like.
 */
class Like extends Model implements LikeContract
{
    /**
     * @var string
     */
    protected $table = 'likes';
    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'type_id',
    ];

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Return the like's author.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }
}
