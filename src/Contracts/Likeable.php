<?php

namespace Turahe\Likeable\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use Turahe\Likeable\Exceptions\LikerNotDefinedException;

/**
 * Interface Likeable.
 */
interface Likeable
{
    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey();

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass();

    /**
     * Collection of the likes on this record.
     *
     * @return MorphMany
     */
    public function likesAndDislikes();

    /**
     * Collection of the likes on this record.
     *
     * @return MorphMany
     */
    public function likes();

    /**
     * Collection of the dislikes on this record.
     *
     * @return MorphMany
     */
    public function dislikes();

    /**
     * Counter is a record that stores the total likes for the morphed record.
     *
     * @return MorphOne
     */
    public function likesCounter();

    /**
     * Counter is a record that stores the total dislikes for the morphed record.
     *
     * @return MorphOne
     */
    public function dislikesCounter();

    /**
     * Fetch users who liked entity.
     *
     * @return Collection
     */
    public function collectLikers();

    /**
     * Fetch users who disliked entity.
     *
     * @return Collection
     */
    public function collectDislikers();

    /**
     * Model likesCount attribute.
     *
     * @return int
     */
    public function getLikesCountAttribute();

    /**
     * Model dislikesCount attribute.
     *
     * @return int
     */
    public function getDislikesCountAttribute();

    /**
     * Did the currently logged in user like this model.
     *
     * @return bool
     */
    public function getLikedAttribute();

    /**
     * Did the currently logged in user dislike this model.
     *
     * @return bool
     */
    public function getDislikedAttribute();

    /**
     * Difference between likes and dislikes count.
     *
     * @return int
     */
    public function getLikesDiffDislikesCountAttribute();

    /**
     * Fetch records that are liked by a given user id.
     *
     * @param  int|null  $userId
     * @return Builder
     *
     * @throws LikerNotDefinedException
     */
    public function scopeWhereLikedBy(Builder $query, $userId = null);

    /**
     * Fetch records that are disliked by a given user id.
     *
     * @param  int|null  $userId
     * @return Builder
     *
     * @throws LikerNotDefinedException
     */
    public function scopeWhereDislikedBy(Builder $query, $userId = null);

    /**
     * Fetch records sorted by likes count.
     *
     * @param  string  $direction
     * @return Builder
     */
    public function scopeOrderByLikesCount(Builder $query, $direction = 'desc');

    /**
     * Fetch records sorted by likes count.
     *
     * @param  string  $direction
     * @return Builder
     */
    public function scopeOrderByDislikesCount(Builder $query, $direction = 'desc');

    /**
     * Add a like for model by the given user.
     *
     * @param  mixed  $userId  If null will use currently logged in user.
     * @return void
     *
     * @throws LikerNotDefinedException
     */
    public function like($userId = null);

    /**
     * Remove a like from this record for the given user.
     *
     * @param  int|null  $userId  If null will use currently logged in user.
     * @return void
     *
     * @throws LikerNotDefinedException
     */
    public function unlike($userId = null);

    /**
     * Toggle like for model by the given user.
     *
     * @param  mixed  $userId  If null will use currently logged in user.
     * @return void
     *
     * @throws LikerNotDefinedException
     */
    public function likeToggle($userId = null);

    /**
     * Has the user already liked likeable model.
     *
     * @param  int|null  $userId
     * @return bool
     */
    public function liked($userId = null);

    /**
     * Delete likes related to the current record.
     *
     * @return void
     */
    public function removeLikes();

    /**
     * Add a dislike for model by the given user.
     *
     * @param  mixed  $userId  If null will use currently logged in user.
     * @return void
     *
     * @throws LikerNotDefinedException
     */
    public function dislike($userId = null);

    /**
     * Remove a dislike from this record for the given user.
     *
     * @param  int|null  $userId  If null will use currently logged in user.
     * @return void
     *
     * @throws LikerNotDefinedException
     */
    public function undislike($userId = null);

    /**
     * Toggle dislike for model by the given user.
     *
     * @param  mixed  $userId  If null will use currently logged in user.
     * @return void
     *
     * @throws LikerNotDefinedException
     */
    public function dislikeToggle($userId = null);

    /**
     * Has the user already disliked likeable model.
     *
     * @param  int|null  $userId
     * @return bool
     */
    public function disliked($userId = null);

    /**
     * Delete dislikes related to the current record.
     *
     * @return void
     */
    public function removeDislikes();
}
