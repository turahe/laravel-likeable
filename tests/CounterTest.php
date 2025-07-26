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

    protected function tearDown(): void
    {
        Schema::drop('books');
    }

    /**
     * @runInSeparateProcess
     */
    public function test_like()
    {
        $likeable = Stub::create(['name' => 'test']);

        $likeable->like(1);

        $this->assertEquals(1, $likeable->likes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_unlike()
    {
        $likeable = Stub::create(['name' => 'test']);

        $likeable->like(1);
        $this->assertEquals(1, $likeable->likes_count);

        $likeable->unlike(1);

        $this->assertEquals(0, $likeable->likes_count);
    }
}
