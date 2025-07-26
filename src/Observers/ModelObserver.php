<?php

namespace Turahe\Likeable\Observers;

use Turahe\Likeable\Contracts\Likeable as LikeableContract;

/**
 * Class ModelObserver.
 */
class ModelObserver
{
    /**
     * Handle the deleted event for the model.
     *
     * @return void
     */
    public function deleted(LikeableContract $likeable)
    {
        if (! $this->removeLikesOnDelete($likeable)) {
            return;
        }

        $likeable->removeLikes();
    }

    /**
     * Should remove likes on model delete (defaults to true).
     *
     * @return bool
     */
    protected function removeLikesOnDelete(LikeableContract $likeable)
    {
        return isset($likeable->removeLikesOnDelete) ? $likeable->removeLikesOnDelete : true;
    }
}
