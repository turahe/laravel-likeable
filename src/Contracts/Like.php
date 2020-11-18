<?php

namespace Turahe\Likeable\Contracts;

/**
 * Interface Like.
 *
 * @property \Turahe\Likeable\Contracts\Likeable likeable
 * @property int type_id
 * @property int user_id
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
