<?php

namespace Turahe\Likeable\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;


/**
 * Class Like
 * @package Turahe\Likeable\Models
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
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }
}
