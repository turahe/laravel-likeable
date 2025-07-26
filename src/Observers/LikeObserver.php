<?php

namespace Turahe\Likeable\Observers;

use Turahe\Likeable\Contracts\Like as LikeContract;
use Turahe\Likeable\Enums\LikeType;
use Turahe\Likeable\Events\ModelWasDisliked;
use Turahe\Likeable\Events\ModelWasLiked;
use Turahe\Likeable\Events\ModelWasUndisliked;
use Turahe\Likeable\Events\ModelWasUnliked;

/**
 * Class LikeObserver.
 */
class LikeObserver
{
    /**
     * Handle the created event for the model.
     *
     * @return void
     */
    public function created(LikeContract $like)
    {
        if ($like->type_id == LikeType::LIKE->value) {
            event(new ModelWasLiked($like->likeable, $like->user_id));
        } else {
            event(new ModelWasDisliked($like->likeable, $like->user_id));
        }
    }

    /**
     * Handle the deleted event for the model.
     *
     * @return void
     */
    public function deleted(LikeContract $like)
    {
        if ($like->type_id == LikeType::LIKE->value) {
            event(new ModelWasUnliked($like->likeable, $like->user_id));
        } else {
            event(new ModelWasUndisliked($like->likeable, $like->user_id));
        }
    }
}
