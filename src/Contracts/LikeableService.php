<?php

namespace Turahe\Likeable\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Turahe\Likeable\Contracts\Likeable as LikeableContract;
use Turahe\Likeable\Enums\LikeType;

/**
 * Interface LikeableService.
 */
interface LikeableService
{
    /**
     * Add a like to likeable model by user.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @param LikeType $type
     * @param string $userId
     * @return void
     *
     * @throws \Turahe\Likeable\Exceptions\LikerNotDefinedException
     */
    public function addLikeTo(LikeableContract $likeable, LikeType $type, $userId);

    /**
     * Remove a like to likeable model by user.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @param LikeType $type
     * @param int|null $userId
     * @return void
     *
     * @throws \Turahe\Likeable\Exceptions\LikerNotDefinedException
     */
    public function removeLikeFrom(LikeableContract $likeable, LikeType $type, $userId);

    /**
     * Toggle like for model by the given user.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @param LikeType $type
     * @param string $userId
     * @return void
     *
     * @throws \Turahe\Likeable\Exceptions\LikerNotDefinedException
     */
    public function toggleLikeOf(LikeableContract $likeable, LikeType $type, $userId);

    /**
     * Has the user already liked likeable model.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @param LikeType $type
     * @param int|null $userId
     * @return bool
     */
    public function isLiked(LikeableContract $likeable, LikeType $type, $userId);

    /**
     * Decrement the total like count stored in the counter.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @return void
     */
    public function decrementLikesCount(LikeableContract $likeable);

    /**
     * Increment the total like count stored in the counter.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @return void
     */
    public function incrementLikesCount(LikeableContract $likeable);

    /**
     * Decrement the total dislike count stored in the counter.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @return void
     */
    public function decrementDislikesCount(LikeableContract $likeable);

    /**
     * Increment the total dislike count stored in the counter.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @return void
     */
    public function incrementDislikesCount(LikeableContract $likeable);

    /**
     * Remove like counters by likeable type.
     *
     * @param string $likeableType
     * @param string|null $type
     * @return void
     */
    public function removeLikeCountersOfType($likeableType, $type = null);

    /**
     * Remove all likes from likeable model.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @param LikeType $type
     * @return void
     */
    public function removeModelLikes(LikeableContract $likeable, LikeType $type);

    /**
     * Get collection of users who liked entity.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @return \Illuminate\Support\Collection
     */
    public function collectLikersOf(LikeableContract $likeable);

    /**
     * Get collection of users who disliked entity.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @return \Illuminate\Support\Collection
     */
    public function collectDislikersOf(LikeableContract $likeable);

    /**
     * Fetch records that are liked by a given user id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param LikeType $type
     * @param int|null $userId
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Turahe\Likeable\Exceptions\LikerNotDefinedException
     */
    public function scopeWhereLikedBy(Builder $query, LikeType $type, $userId);

    /**
     * Fetch records sorted by likes count.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param LikeType $likeType
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByLikesCount(Builder $query, LikeType $likeType, $direction = 'desc');

    /**
     * Fetch likes counters data.
     *
     * @param string $likeableType
     * @param string $likeType
     * @return array
     */
    public function fetchLikesCounters($likeableType, $likeType);
}
