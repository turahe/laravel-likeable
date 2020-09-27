<?php

namespace Turahe\Likeable;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @mixin \Eloquent
 * @property Likeable likeable
 * @property string user_id
 * @property string likeable_id
 * @property string likeable_type
 */
class Like extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * @var string
     */
    protected $table = 'likes';
    /**
     * @var string[]
     */
    protected $fillable = [
	    'likeable_id',
        'likeable_type',
        'user_id'
    ];

    /**
     * @access private
     */
	public function likeable(): MorphTo
	{
		return $this->morphTo();
	}
    /**
     * Return the like's author.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @derivated
     * @return LikeFactory
     */
    protected static function newFactory()
    {
        return LikeFactory::new();
    }
}
