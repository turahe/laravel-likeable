<?php

namespace Turahe\Likeable\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Turahe\Likeable\Contracts\Like as LikeContract;
use Turahe\Likeable\LikeFactory;


/**
 * Class Like
 * @package Turahe\Likeable\Models
 */
class Like extends Model implements LikeContract
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
        'user_id',
        'type_id',
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
