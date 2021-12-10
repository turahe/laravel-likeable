<?php

namespace Turahe\Tests\Likeable\Models;

use Turahe\Likeable\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;

/**
 * Turahe\Tests\Likeable\Models\LikeableStub
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Turahe\Likeable\Models\Like[] $dislikes
 * @property-read int $dislikes_count
 * @property-read \Turahe\Likeable\Models\LikeCounter|null $dislikesCounter
 * @property-read bool $disliked
 * @property-read bool $liked
 * @property-read int|null $likes_count
 * @property-read int $likes_diff_dislikes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Turahe\Likeable\Models\Like[] $likes
 * @property-read \Illuminate\Database\Eloquent\Collection|\Turahe\Likeable\Models\Like[] $likesAndDislikes
 * @property-read int|null $likes_and_dislikes_count
 * @property-read \Turahe\Likeable\Models\LikeCounter|null $likesCounter
 * @method static \Illuminate\Database\Eloquent\Builder|LikeableStub newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LikeableStub newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LikeableStub orderByDislikesCount($direction = 'desc')
 * @method static \Illuminate\Database\Eloquent\Builder|LikeableStub orderByLikesCount($direction = 'desc')
 * @method static \Illuminate\Database\Eloquent\Builder|LikeableStub query()
 * @method static \Illuminate\Database\Eloquent\Builder|LikeableStub whereDislikedBy($userId = null)
 * @method static \Illuminate\Database\Eloquent\Builder|LikeableStub whereLikedBy($userId = null)
 * @mixin \Eloquent
 */
class LikeableStub extends Model
{
    use Likeable;

    public function incrementLikeCount()
    {
    }

    public function decrementLikeCount()
    {
    }
}
