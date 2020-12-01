<?php

namespace Turahe\Tests\Likeable\Models;

use Turahe\Likeable\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;

class LikeableStub extends Model
{
    use Likeable;

    public function incrementLikeCount()
    {
    }

    public function decrementLikeCount()
    {
    }
}
