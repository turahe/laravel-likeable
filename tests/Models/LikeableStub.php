<?php


namespace Turahe\Tests\Likeable\Models;


use Illuminate\Database\Eloquent\Model;
use Turahe\Likeable\Likeable;

class LikeableStub extends Model
{
    use Likeable;

    public function incrementLikeCount() {}
    public function decrementLikeCount() {}

}
