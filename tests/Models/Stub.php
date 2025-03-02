<?php

namespace Turahe\Tests\Likeable\Models;

use Turahe\Likeable\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;

class Stub extends Model implements \Turahe\Likeable\Contracts\Likeable
{
    use Likeable;

    public $table = 'books';
}
