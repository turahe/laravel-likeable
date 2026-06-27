<?php

namespace Turahe\Likeable\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Interface Like.
 */
interface Like
{
    /**
     * Likeable model relation.
     *
     * @return MorphTo
     */
    public function likeable();
}
