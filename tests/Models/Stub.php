<?php

namespace Turahe\Tests\Likeable\Models;

use Illuminate\Database\Eloquent\Model;
use Turahe\Likeable\Likeable;


/**
 * @mixin \Eloquent
 */
class Stub extends Model
{
    use Likeable;

    public $table = 'books';
}
