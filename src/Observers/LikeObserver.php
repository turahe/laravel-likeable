<?php

namespace Turahe\Likeable\Observers;

use Turahe\Likeable\Enums\LikeType;
use Turahe\Likeable\Events\ModelWasLiked;
use Turahe\Likeable\Events\ModelWasUnliked;
use Turahe\Likeable\Events\ModelWasDisliked;
use Turahe\Likeable\Events\ModelWasUndisliked;
use Turahe\Likeable\Contracts\Like as LikeContract;
use Turahe\Likeable\Contracts\LikeableService as LikeableServiceContract;

/**
 * Class LikeObserver.
 */
class LikeObserver
{
    /**
     * Handle the created event for the model.
     *
     * @param \Turahe\Likeable\Contracts\Like $like
     * @return void
     */
    public function created(LikeContract $like)
    {
        if ($like->type_id == LikeType::LIKE) {
            event(new ModelWasLiked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->incrementLikesCount($like->likeable);
        } else {
            event(new ModelWasDisliked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->incrementDislikesCount($like->likeable);
        }
    }

    /**
     * Handle the deleted event for the model.
     *
     * @param \Turahe\Likeable\Contracts\Like $like
     * @return void
     */
    public function deleted(LikeContract $like)
    {
        if ($like->type_id == LikeType::LIKE) {
            event(new ModelWasUnliked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->decrementLikesCount($like->likeable);
        } else {
            event(new ModelWasUndisliked($like->likeable, $like->user_id));
            app(LikeableServiceContract::class)->decrementDislikesCount($like->likeable);
        }
    }
}
