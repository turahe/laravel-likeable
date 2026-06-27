<?php

namespace Turahe\Likeable\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Interface LikeCounter.
 */
interface LikeCounter
{
    /**
     * Likeable model relation.
     *
     * @return MorphTo
     */
    public function likeable();
}
