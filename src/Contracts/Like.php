<?php

namespace Turahe\Likeable\Contracts;

/**
 * Interface Like.
 */
interface Like
{
    /**
     * Likeable model relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function likeable();
}
