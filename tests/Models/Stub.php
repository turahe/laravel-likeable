<?php

namespace Turahe\Tests\Likeable\Models;

use Illuminate\Database\Eloquent\Model;
use Turahe\Likeable\Traits\Likeable;

class Stub extends Model implements \Turahe\Likeable\Contracts\Likeable
{
    use Likeable;

    public $table = 'books';

    protected $fillable = ['name'];
}
