<?php

namespace Turahe\Tests\Likeable\Models;

use Turahe\Likeable\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
class Stub extends Model
{
    use Likeable;

    public $table = 'books';
}
