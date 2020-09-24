<?php

namespace Turahe\Likeable;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 * @property Likeable likeable
 * @property string user_id
 * @property string likeable_id
 * @property string likeable_type
 */
class Like extends Model
{
	protected $table = 'likes';
	public $timestamps = true;
	protected $fillable = ['likeable_id', 'likeable_type', 'user_id'];

    /**
     * @access private
     */
	public function likeable()
	{
		return $this->morphTo();
	}
}
