<?php


namespace Turahe\Likeable\Events;

use Turahe\Likeable\Contracts\Likeable as LikeableContract;

/**
 * Class ModelWasUndisliked
 * @package Turahe\Likeable\Events
 */
class ModelWasUndisliked
{
    /**
     * The un disliked model.
     *
     * @var \Turahe\Likeable\Contracts\Likeable
     */
    public $model;

    /**
     * User id who unliked model.
     *
     * @var int
     */
    public $likerId;

    /**
     * Create a new event instance.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @param int $likerId
     * @return void
     */
    public function __construct(LikeableContract $likeable, $likerId)
    {
        $this->model = $likeable;
        $this->likerId = $likerId;
    }

}
