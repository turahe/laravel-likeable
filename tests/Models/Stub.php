<?php

namespace Turahe\Tests\Likeable\Models;

use Turahe\Likeable\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;

/**
 * Turahe\Tests\Likeable\Models\Stub
 *
 * @mixin \Eloquent
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
 * @method static \Illuminate\Database\Eloquent\Builder|Stub newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stub newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Stub orderByDislikesCount($direction = 'desc')
 * @method static \Illuminate\Database\Eloquent\Builder|Stub orderByLikesCount($direction = 'desc')
 * @method static \Illuminate\Database\Eloquent\Builder|Stub query()
 * @method static \Illuminate\Database\Eloquent\Builder|Stub whereDislikedBy($userId = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Stub whereLikedBy($userId = null)
 */
class Stub extends Model
{
    use Likeable;

    public $table = 'books';
}
