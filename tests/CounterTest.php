<?php

namespace Turahe\Tests\Likeable;

use Mockery as m;

class CounterTest extends BaseTestCase
{
	public function testLike()
	{
		$likeable = m::mock('Turahe\Tests\Likeable\Models\LikeableStub[incrementLikeCount]');
		$likeable->shouldReceive('incrementLikeCount')->andReturn(null);

		$likeable->like(0);
	}

	public function testUnlike()
	{
		$likeable = m::mock('Turahe\Tests\Likeable\Models\LikeableStub[decrementLikeCount]');
		$likeable->shouldReceive('decrementLikeCount')->andReturn(null);

		$likeable->unlike(0);
	}

}

