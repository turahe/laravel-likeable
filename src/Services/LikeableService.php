<?php

namespace Turahe\Likeable\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Turahe\Likeable\Contracts\Like as LikeContract;
use Turahe\Likeable\Contracts\Likeable;
use Turahe\Likeable\Contracts\Likeable as LikeableContract;
use Turahe\Likeable\Contracts\LikeableService as LikeableServiceContract;
use Turahe\Likeable\Contracts\LikeCounter as LikeCounterContract;
use Turahe\Likeable\Enums\LikeType;
use Turahe\Likeable\Events\ModelWasDisliked;
use Turahe\Likeable\Events\ModelWasLiked;
use Turahe\Likeable\Events\ModelWasUndisliked;
use Turahe\Likeable\Events\ModelWasUnliked;
use Turahe\Likeable\Exceptions\LikerNotDefinedException;
use Turahe\Likeable\Exceptions\LikeTypeInvalidException;

class LikeableService implements LikeableServiceContract
{
    /**
     * Add a like to likeable model by user.
     *
     * @param  int|string|null  $userId
     * @return void
     *
     * @throws LikerNotDefinedException
     * @throws LikeTypeInvalidException
     */
    public function addLikeTo(LikeableContract $likeable, LikeType $type, $userId)
    {
        $userId = $this->getLikerUserId($userId);

        $like = $likeable->likesAndDislikes()->where([
            'user_id' => $userId,
        ])->first();

        if (! $like) {
            $newLike = $likeable->likes()->create([
                'user_id' => $userId,
                'type_id' => $this->getLikeTypeId($type),
            ]);

            // Update counters and dispatch events
            if ($type === LikeType::LIKE) {
                $this->incrementLikesCount($likeable);
                event(new ModelWasLiked($likeable, $userId));
            } else {
                $this->incrementDislikesCount($likeable);
                event(new ModelWasDisliked($likeable, $userId));
            }

            return;
        }

        // If like already exists with same type, don't do anything
        if ($like->type_id == $this->getLikeTypeId($type)) {
            return;
        }

        // If like exists with different type, delete it and create new one
        $oldType = $like->type_id;
        $like->delete();

        // Decrement the old type counter
        if ($oldType == LikeType::LIKE->value) {
            $this->decrementLikesCount($likeable);
        } else {
            $this->decrementDislikesCount($likeable);
        }

        $newLike = $likeable->likes()->create([
            'user_id' => $userId,
            'type_id' => $this->getLikeTypeId($type),
        ]);

        // Update counters and dispatch events
        if ($type === LikeType::LIKE) {
            $this->incrementLikesCount($likeable);
            event(new ModelWasLiked($likeable, $userId));
        } else {
            $this->incrementDislikesCount($likeable);
            event(new ModelWasDisliked($likeable, $userId));
        }
    }

    /**
     * Remove a like to likeable model by user.
     *
     * @param  int|null  $userId
     * @return void
     *
     * @throws LikerNotDefinedException
     * @throws LikeTypeInvalidException
     */
    public function removeLikeFrom(LikeableContract $likeable, LikeType $type, $userId)
    {
        $userId = $this->getLikerUserId($userId);
        $typeId = $this->getLikeTypeId($type);

        $like = $likeable->likesAndDislikes()->where([
            'user_id' => $userId,
            'type_id' => $typeId,
        ])->first();

        if (! $like) {
            return;
        }

        // Store the type before deleting the like
        $likeType = $like->type_id;
        $likeUserId = $like->user_id;

        $like->delete();

        // Update counters and dispatch events
        if ($likeType == LikeType::LIKE->value) {
            $this->decrementLikesCount($likeable);
            event(new ModelWasUnliked($likeable, $likeUserId));
        } else {
            $this->decrementDislikesCount($likeable);
            event(new ModelWasUndisliked($likeable, $likeUserId));
        }
    }

    /**
     * Toggle like for model by the given user.
     *
     * @param  string  $userId
     * @return void
     *
     * @throws LikerNotDefinedException
     * @throws LikeTypeInvalidException
     */
    public function toggleLikeOf(LikeableContract $likeable, LikeType $type, $userId)
    {
        $userId = $this->getLikerUserId($userId);

        $like = $likeable->likesAndDislikes()->where([
            'user_id' => $userId,
            'type_id' => $this->getLikeTypeId($type),
        ])->exists();

        if ($like) {
            $this->removeLikeFrom($likeable, $type, $userId);
        } else {
            $this->addLikeTo($likeable, $type, $userId);
        }
    }

    /**
     * Has the user already liked likeable model.
     *
     * @param  int|null  $userId
     * @return bool
     *
     * @throws LikeTypeInvalidException
     */
    public function isLiked(LikeableContract $likeable, LikeType $type, $userId)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        if ($userId === null) {
            return false;
        }

        $typeId = $this->getLikeTypeId($type);

        $exists = $this->hasLikeOrDislikeInLoadedRelation($likeable, $typeId, $userId);
        if (! is_null($exists)) {
            return $exists;
        }

        return $likeable->likesAndDislikes()->where([
            'user_id' => $userId,
            'type_id' => $typeId,
        ])->exists();
    }

    /**
     * Decrement the total like count stored in the counter.
     *
     * @return void
     */
    public function decrementLikesCount(LikeableContract $likeable)
    {
        $counter = $likeable->likesCounter()->first();

        if (! $counter) {
            return;
        }

        $counter->decrement('count');

        // Refresh the relationship to reflect the updated count
        $likeable->load('likesCounter');
    }

    /**
     * Increment the total like count stored in the counter.
     *
     * @return void
     */
    public function incrementLikesCount(LikeableContract $likeable)
    {
        $counter = $likeable->likesCounter()->first();

        if (! $counter) {
            $counter = $likeable->likesCounter()->create([
                'count' => 0,
                'type_id' => LikeType::LIKE->value,
            ]);
        }

        $counter->increment('count');
    }

    /**
     * Decrement the total dislike count stored in the counter.
     *
     * @return void
     */
    public function decrementDislikesCount(LikeableContract $likeable)
    {
        $counter = $likeable->dislikesCounter()->first();

        if (! $counter) {
            return;
        }

        $counter->decrement('count');

        // Refresh the relationship to reflect the updated count
        $likeable->load('dislikesCounter');

        // If counter reaches 0, delete it to maintain consistency
        if ($counter->fresh()->count <= 0) {
            $counter->delete();
            $likeable->load('dislikesCounter');
        }
    }

    /**
     * Increment the total dislike count stored in the counter.
     *
     * @return void
     */
    public function incrementDislikesCount(LikeableContract $likeable)
    {
        $counter = $likeable->dislikesCounter()->first();

        if (! $counter) {
            $counter = $likeable->dislikesCounter()->create([
                'count' => 0,
                'type_id' => LikeType::DISLIKE->value,
            ]);
        }

        $counter->increment('count');
    }

    /**
     * Remove like counters by likeable type.
     *
     * @param  string  $likeableType
     * @param  string|null  $type
     * @return void
     *
     * @throws LikeTypeInvalidException
     */
    public function removeLikeCountersOfType($likeableType, $type = null)
    {
        if (class_exists($likeableType)) {
            /** @var Likeable $likeable */
            $likeable = new $likeableType;
            $likeableType = $likeable->getMorphClass();
        }

        /** @var Builder $counters */
        $counters = app(LikeCounterContract::class)->where('likeable_type', $likeableType);
        if (! is_null($type)) {
            $counters->where('type_id', $this->getLikeTypeId($type));
        }
        $counters->delete();
    }

    /**
     * Remove all likes from likeable model.
     *
     * @return void
     *
     * @throws LikeTypeInvalidException
     */
    public function removeModelLikes(LikeableContract $likeable, LikeType $type)
    {
        app(LikeContract::class)->where([
            'likeable_id' => $likeable->getKey(),
            'likeable_type' => $likeable->getMorphClass(),
            'type_id' => $this->getLikeTypeId($type),
        ])->delete();

        app(LikeCounterContract::class)->where([
            'likeable_id' => $likeable->getKey(),
            'likeable_type' => $likeable->getMorphClass(),
            'type_id' => $this->getLikeTypeId($type),
        ])->delete();
    }

    /**
     * Get collection of users who liked entity.
     *
     * @return Collection
     */
    public function collectLikersOf(LikeableContract $likeable)
    {
        $userModel = $this->resolveUserModel();

        $likersIds = $likeable->likes->pluck('user_id');

        return $userModel::whereKey($likersIds)->get();
    }

    /**
     * Get collection of users who disliked entity.
     *
     * @return Collection
     */
    public function collectDislikersOf(LikeableContract $likeable)
    {
        $userModel = $this->resolveUserModel();

        $likersIds = $likeable->dislikes->pluck('user_id');

        return $userModel::whereKey($likersIds)->get();
    }

    /**
     * Fetch records that are liked by a given user id.
     *
     * @param  int|null  $userId
     * @return Builder
     *
     * @throws LikerNotDefinedException
     */
    public function scopeWhereLikedBy(Builder $query, LikeType $type, $userId)
    {
        $userId = $this->getLikerUserId($userId);

        return $query->whereHas('likesAndDislikes', function (Builder $innerQuery) use ($type, $userId) {
            $innerQuery->where('user_id', $userId);
            $innerQuery->where('type_id', $this->getLikeTypeId($type));
        });
    }

    /**
     * Fetch records sorted by likes count.
     *
     * @param  string  $direction
     * @return Builder
     */
    public function scopeOrderByLikesCount(Builder $query, LikeType $likeType, $direction = 'desc')
    {
        $likeable = $query->getModel();

        return $query
            ->select($likeable->getTable().'.*', 'like_counters.count')
            ->leftJoin('like_counters', function (JoinClause $join) use ($likeable, $likeType) {
                $join
                    ->on('like_counters.likeable_id', '=', "{$likeable->getTable()}.{$likeable->getKeyName()}")
                    ->where('like_counters.likeable_type', '=', $likeable->getMorphClass())
                    ->where('like_counters.type_id', '=', $this->getLikeTypeId($likeType));
            })
            ->orderBy('like_counters.count', $direction);
    }

    /**
     * Fetch likes counters data.
     *
     * @param  string  $likeableType
     * @param  string  $likeType
     * @return array
     *
     * @throws LikeTypeInvalidException
     */
    public function fetchLikesCounters($likeableType, $likeType)
    {
        if (class_exists($likeableType)) {
            /** @var Likeable $likeable */
            $likeable = new $likeableType;
            $likeableType = $likeable->getMorphClass();
        }

        /** @var Builder $likesCount */
        $likesCount = app(LikeContract::class)
            ->select([
                DB::raw('COUNT(*) AS count'),
                'likeable_type',
                'likeable_id',
                'type_id',
            ])
            ->where('likeable_type', $likeableType);

        if (! is_null($likeType)) {
            $likesCount->where('type_id', $this->getLikeTypeId($likeType));
        }

        $likesCount->groupBy('likeable_id', 'type_id');

        return $likesCount->get()->toArray();
    }

    /**
     * Get current user id or get user id passed in.
     *
     * @param  int|string|null  $userId
     * @return int|string
     *
     * @throws LikerNotDefinedException
     */
    protected function getLikerUserId($userId)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        if ($userId === null) {
            throw new LikerNotDefinedException;
        }

        return $userId;
    }

    /**
     * Fetch the primary ID of the currently logged in user.
     *
     * @return int
     */
    protected function loggedInUserId()
    {
        return auth()->id();
    }

    /**
     * Get like type id from name or LikeType enum.
     *
     * @todo move to Enum class
     *
     * @throws LikeTypeInvalidException
     */
    protected function getLikeTypeId(LikeType|string $type): string
    {
        if ($type instanceof LikeType) {
            return $type->value;
        }

        $enum = LikeType::tryFrom(strtolower($type));

        if ($enum !== null) {
            return $enum->value;
        }

        throw new LikeTypeInvalidException("Like type `{$type}` not exist");
    }

    /**
     * Retrieve User's model class name.
     *
     * @return Authenticatable
     */
    private function resolveUserModel()
    {
        return config('auth.providers.users.model');
    }

    /**
     * @param  string  $typeId
     * @param  int|string  $userId
     * @return bool|null
     *
     * @throws LikeTypeInvalidException
     */
    private function hasLikeOrDislikeInLoadedRelation(LikeableContract $likeable, $typeId, $userId)
    {
        $relations = $this->likeTypeRelations($typeId);

        foreach ($relations as $relation) {
            if (! $likeable->relationLoaded($relation)) {
                continue;
            }

            return $likeable->{$relation}->contains(function ($item) use ($userId, $typeId) {
                return $item->user_id == $userId && $item->type_id === $typeId;
            });
        }

        return null;
    }

    /**
     * Resolve list of likeable relations by like type.
     *
     * @param  string|LikeType  $type
     * @return array
     *
     * @throws LikeTypeInvalidException
     */
    private function likeTypeRelations($type)
    {
        if ($type instanceof LikeType) {
            $typeKey = $type->value;
        } elseif (defined('\\Turahe\\Likeable\\Enums\\LikeType::'.strtoupper($type))) {
            $typeKey = constant('\\Turahe\\Likeable\\Enums\\LikeType::'.strtoupper($type))->value;
        } else {
            throw new LikeTypeInvalidException("Like type `{$type}` not supported");
        }
        $relations = [
            LikeType::LIKE->value => [
                'likes',
                'likesAndDislikes',
            ],
            LikeType::DISLIKE->value => [
                'dislikes',
                'likesAndDislikes',
            ],
        ];
        if (! isset($relations[$typeKey])) {
            throw new LikeTypeInvalidException("Like type `{$type}` not supported");
        }

        return $relations[$typeKey];
    }
}
