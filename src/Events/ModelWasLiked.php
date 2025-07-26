<?php

namespace Turahe\Likeable\Events;

use Turahe\Likeable\Contracts\Likeable as LikeableContract;

/**
 * Class ModelWasLiked.
 */
class ModelWasLiked
{
    /**
     * The liked model.
     *
     * @var \Turahe\Likeable\Contracts\Likeable
     */
    public $likeable;

    /**
     * User id who liked model.
     *
     * @var int
     */
    public $userId;

    /**
     * Create a new event instance.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @param int $likerId
     * @return void
     */
    public function __construct(LikeableContract $likeable, $userId)
    {
        $this->likeable = $likeable;
        $this->userId = $userId;
    }
}
