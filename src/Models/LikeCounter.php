<?php

namespace Turahe\Likeable\Models;

use Illuminate\Database\Eloquent\Model;
use Turahe\Likeable\Contracts\LikeCounter as LikeCounterContract;

/**
 * Class LikeCounter.
 */
class LikeCounter extends Model implements LikeCounterContract
{
    protected $table = 'like_counters';
    protected $fillable = [
        'type_id',
        'count',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'count' => 'integer',
    ];

    /**
     * Likeable model relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function likeable()
    {
        return $this->morphTo();
    }
}
