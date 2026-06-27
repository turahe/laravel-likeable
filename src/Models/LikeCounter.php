<?php

namespace Turahe\Likeable\Models;

use Illuminate\Database\Eloquent\Model;
use Turahe\Likeable\Contracts\LikeableService as LikeableServiceContract;
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
 *
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
 *
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

    public static function rebuild(string $modelClass, ?string $type = null): void
    {
        $model = new $modelClass;
        $modelType = $model->getMorphClass();
        $service = app(LikeableServiceContract::class);

        $service->removeLikeCountersOfType($modelType, $type);

        foreach ($service->fetchLikesCounters($modelType, $type) as $counter) {
            static::create([
                'likeable_type' => $counter['likeable_type'],
                'likeable_id' => $counter['likeable_id'],
                'type_id' => $counter['type_id'],
                'count' => $counter['count'],
            ]);
        }
    }
}
