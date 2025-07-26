<?php

namespace Turahe\Tests\Likeable;

use Turahe\Tests\Likeable\Models\Stub;
use Illuminate\Support\Facades\Schema;

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
        Schema::drop('books');
    }

    /**
     * @runInSeparateProcess
     */
    public function testLike()
    {
        $likeable = Stub::create(['name' => 'test']);
        
        $likeable->like(1);
        
        $this->assertEquals(1, $likeable->likes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function testUnlike()
    {
        $likeable = Stub::create(['name' => 'test']);
        
        $likeable->like(1);
        $this->assertEquals(1, $likeable->likes_count);
        
        $likeable->unlike(1);
        
        $this->assertEquals(0, $likeable->likes_count);
    }
}
