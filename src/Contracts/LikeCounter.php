<?php

namespace Turahe\Likeable\Contracts;

/**
 * Interface LikeCounter.
 */
interface LikeCounter
{
    /**
     * Likeable model relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function likeable();
}
