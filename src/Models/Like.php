<?php

namespace Turahe\Likeable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Turahe\Likeable\Contracts\Like as LikeContract;

/**
 * Class Like.
 *
 * @property int $id
 * @property string $likeable_type
 * @property int $likeable_id
 * @property int $user_id
 * @property string $type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $author
 * @property-read Model|\Eloquent $likeable
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Like newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Like newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Like query()
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereLikeableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereLikeableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Like whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Like extends Model implements LikeContract
{
    /**
     * @var string
     */
    protected $table = 'likes';

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'type_id',
    ];

    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Return the like's author.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }
}
