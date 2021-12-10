<?php

namespace Turahe\Likeable\Models;

use Illuminate\Database\Eloquent\Model;
use Turahe\Likeable\Contracts\LikeCounter as LikeCounterContract;

/**
 * Class LikeCounter.
 *
 * @property int $id
 * @property string $likeable_type
 * @property int $likeable_id
 * @property string $type_id
 * @property int $count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $likeable
 * @method static \Illuminate\Database\Eloquent\Builder|LikeCounter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LikeCounter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LikeCounter query()
 * @method static \Illuminate\Database\Eloquent\Builder|LikeCounter whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LikeCounter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LikeCounter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LikeCounter whereLikeableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LikeCounter whereLikeableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LikeCounter whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LikeCounter whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LikeCounter extends Model implements LikeCounterContract
{
    protected $table = 'like_counters';
    protected $fillable = [
        'type_id',
        'count',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'count' => 'integer',
    ];

    /**
     * Likeable model relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function likeable()
    {
        return $this->morphTo();
    }
}
