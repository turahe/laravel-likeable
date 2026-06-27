<?php

namespace Turahe\Tests\Likeable;

use Illuminate\Support\Facades\Schema;
use Turahe\Tests\Likeable\Models\Stub;

class CounterTest extends BaseTestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        Schema::create('books', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testLike()
    {
        $stub = Stub::create(['name' => 'test']);

        $stub->like(1);

        $this->assertEquals(1, $stub->likesCount);
    }

    public function testUnlike()
    {
        $stub = Stub::create(['name' => 'test']);

        $stub->like(1);
        $stub->unlike(1);

        $this->assertEquals(0, $stub->likesCount);
    }
}
