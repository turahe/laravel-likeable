<?php

namespace Turahe\Likeable\Services;

use Illuminate\Support\Facades\DB;
use Turahe\Likeable\Enums\LikeType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Turahe\Likeable\Contracts\Like as LikeContract;
use Turahe\Likeable\Exceptions\LikerNotDefinedException;
use Turahe\Likeable\Exceptions\LikeTypeInvalidException;
use Turahe\Likeable\Contracts\Likeable as LikeableContract;
use Turahe\Likeable\Contracts\LikeCounter as LikeCounterContract;
use Turahe\Likeable\Contracts\LikeableService as LikeableServiceContract;

class LikeableService implements LikeableServiceContract
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
     * @throws \Turahe\Likeable\Exceptions\LikeTypeInvalidException
     */
    public function addLikeTo(LikeableContract $likeable, LikeType $type, $userId)
    {
        $userId = $this->getLikerUserId($userId);

        $like = $likeable->likesAndDislikes()->where([
            'user_id' => $userId,
        ])->first();

        if (! $like) {
            $likeable->likes()->create([
                'user_id' => $userId,
                'type_id' => $this->getLikeTypeId($type),
            ]);

            return;
        }

        if ($like->type_id == $this->getLikeTypeId($type)) {
            return;
        }

        $like->delete();

        $likeable->likes()->create([
            'user_id' => $userId,
            'type_id' => $this->getLikeTypeId($type),
        ]);
    }

    /**
     * Remove a like to likeable model by user.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @param LikeType $type
     * @param int|null $userId
     * @return void
     *
     * @throws \Turahe\Likeable\Exceptions\LikerNotDefinedException
     * @throws \Turahe\Likeable\Exceptions\LikeTypeInvalidException
     */
    public function removeLikeFrom(LikeableContract $likeable, LikeType $type, $userId)
    {
        $like = $likeable->likesAndDislikes()->where([
            'user_id' => $this->getLikerUserId($userId),
            'type_id' => $this->getLikeTypeId($type),
        ])->first();

        if (! $like) {
            return;
        }

        // Store the type before deleting the like
        $likeType = $like->type_id;

        $like->delete();

        // Explicitly update the counter since the observer might not be triggered
        if ($likeType == LikeType::LIKE->value) {
            $this->decrementLikesCount($likeable);
        } else {
            $this->decrementDislikesCount($likeable);
        }
    }

    /**
     * Toggle like for model by the given user.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @param LikeType $type
     * @param string $userId
     * @return void
     *
     * @throws \Turahe\Likeable\Exceptions\LikerNotDefinedException
     * @throws \Turahe\Likeable\Exceptions\LikeTypeInvalidException
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
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @param LikeType $type
     * @param int|null $userId
     * @return bool
     *
     * @throws \Turahe\Likeable\Exceptions\LikeTypeInvalidException
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
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
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
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @return void
     */
    public function incrementLikesCount(LikeableContract $likeable)
    {
        $counter = $likeable->likesCounter()->first();

        if (! $counter) {
            $counter = $likeable->likesCounter()->create([
                'count' => 0,
                'type_id' => LikeType::LIKE,
            ]);
        }

        $counter->increment('count');
    }

    /**
     * Decrement the total dislike count stored in the counter.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
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
    }

    /**
     * Increment the total dislike count stored in the counter.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @return void
     */
    public function incrementDislikesCount(LikeableContract $likeable)
    {
        $counter = $likeable->dislikesCounter()->first();

        if (! $counter) {
            $counter = $likeable->dislikesCounter()->create([
                'count' => 0,
                'type_id' => LikeType::DISLIKE,
            ]);
        }

        $counter->increment('count');
    }

    /**
     * Remove like counters by likeable type.
     *
     * @param string $likeableType
     * @param string|null $type
     * @return void
     *
     * @throws \Turahe\Likeable\Exceptions\LikeTypeInvalidException
     */
    public function removeLikeCountersOfType($likeableType, $type = null)
    {
        if (class_exists($likeableType)) {
            /** @var \Turahe\Likeable\Contracts\Likeable $likeable */
            $likeable = new $likeableType;
            $likeableType = $likeable->getMorphClass();
        }

        /** @var \Illuminate\Database\Eloquent\Builder $counters */
        $counters = app(LikeCounterContract::class)->where('likeable_type', $likeableType);
        if (! is_null($type)) {
            $counters->where('type_id', $this->getLikeTypeId($type));
        }
        $counters->delete();
    }

    /**
     * Remove all likes from likeable model.
     *
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @param LikeType $type
     * @return void
     *
     * @throws \Turahe\Likeable\Exceptions\LikeTypeInvalidException
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
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @return \Illuminate\Support\Collection
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
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @return \Illuminate\Support\Collection
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
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param LikeType $type
     * @param int|null $userId
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Turahe\Likeable\Exceptions\LikerNotDefinedException
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
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param LikeType $likeType
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
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
     * @param string $likeableType
     * @param string $likeType
     * @return array
     *
     * @throws \Turahe\Likeable\Exceptions\LikeTypeInvalidException
     */
    public function fetchLikesCounters($likeableType, $likeType)
    {
        /** @var \Illuminate\Database\Eloquent\Builder $likesCount */
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
     * @param int $userId
     * @return int
     *
     * @throws \Turahe\Likeable\Exceptions\LikerNotDefinedException
     */
    protected function getLikerUserId($userId)
    {
        if (is_null($userId)) {
            $userId = $this->loggedInUserId();
        }

        if ($userId === null) {
            throw new LikerNotDefinedException();
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
     * @param string|LikeType $type
     * @return int
     *
     * @throws \Turahe\Likeable\Exceptions\LikeTypeInvalidException
     */
    protected function getLikeTypeId($type)
    {
        if ($type instanceof LikeType) {
            $typeName = $type->name;
        } else {
            $typeName = strtoupper($type);
        }
        if (!defined("\\Turahe\\Likeable\\Enums\\LikeType::{$typeName}")) {
            throw new LikeTypeInvalidException("Like type `{$typeName}` not exist");
        }
        return constant("\\Turahe\\Likeable\\Enums\\LikeType::{$typeName}");
    }

    /**
     * Retrieve User's model class name.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    private function resolveUserModel()
    {
        return config('auth.providers.users.model');
    }

    /**
     * @param \Turahe\Likeable\Contracts\Likeable $likeable
     * @param string $typeId
     * @param int $userId
     * @return bool|null
     *
     * @throws \Turahe\Likeable\Exceptions\LikeTypeInvalidException
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
     * @param string|LikeType $type
     * @return array
     *
     * @throws \Turahe\Likeable\Exceptions\LikeTypeInvalidException
     */
    private function likeTypeRelations($type)
    {
        if ($type instanceof LikeType) {
            $typeKey = $type->value;
        } elseif (defined("\\Turahe\\Likeable\\Enums\\LikeType::" . strtoupper($type))) {
            $typeKey = constant("\\Turahe\\Likeable\\Enums\\LikeType::" . strtoupper($type))->value;
        } else {
            throw new LikeTypeInvalidException("Like type `{$type}` not supported");
        }
        $relations = [
            'like' => [
                'likes',
                'likesAndDislikes',
            ],
            'dislike' => [
                'dislikes',
                'likesAndDislikes',
            ],
        ];
        if (!isset($relations[$typeKey])) {
            throw new LikeTypeInvalidException("Like type `{$type}` not supported");
        }
        return $relations[$typeKey];
    }
}
