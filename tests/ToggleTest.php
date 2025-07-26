<?php

namespace Turahe\Tests\Likeable;

use Turahe\Likeable\Models\Like;
use Illuminate\Support\Facades\Schema;
use Turahe\Tests\Likeable\Models\Stub;
use Illuminate\Database\Eloquent\Model;

class ToggleTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Model::unguard();
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        Schema::create('users', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('books', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
    }



    public function tearDown(): void
    {
        Schema::drop('books');
        Schema::drop('users');
    }

    /**
     * @runInSeparateProcess
     */
    public function test_like_toggle()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name'=>123]);

        // First toggle should add like
        $stub->likeToggle(1);
        $this->assertEquals(1, $stub->likes_count);
        $this->assertTrue($stub->liked(1));

        // Second toggle should remove like
        $stub->likeToggle(1);
        $this->assertEquals(0, $stub->likes_count);
        $this->assertFalse($stub->liked(1));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_dislike_toggle()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name'=>123]);

        // First toggle should add dislike
        $stub->dislikeToggle(1);
        $this->assertEquals(1, $stub->dislikes_count);
        $this->assertTrue($stub->disliked(1));

        // Second toggle should remove dislike
        $stub->dislikeToggle(1);
        $this->assertEquals(0, $stub->dislikes_count);
        $this->assertFalse($stub->disliked(1));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_toggle_switches_between_like_and_dislike()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name'=>123]);

        // Start with like
        $stub->like(1);
        $this->assertEquals(1, $stub->likes_count);
        $this->assertEquals(0, $stub->dislikes_count);
        $this->assertTrue($stub->liked(1));
        $this->assertFalse($stub->disliked(1));

        // Toggle to dislike
        $stub->dislikeToggle(1);
        $stub->refresh();
        $this->assertEquals(0, $stub->likes_count);
        $this->assertEquals(1, $stub->dislikes_count);
        $this->assertFalse($stub->liked(1));
        $this->assertTrue($stub->disliked(1));

        // Toggle back to like
        $stub->likeToggle(1);
        $stub->refresh();
        $this->assertEquals(1, $stub->likes_count);
        $this->assertEquals(0, $stub->dislikes_count);
        $this->assertTrue($stub->liked(1));
        $this->assertFalse($stub->disliked(1));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_multiple_users_toggle()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name'=>123]);

        // User 1 toggles like
        $stub->likeToggle(1);
        $this->assertEquals(1, $stub->likes_count);
        $this->assertTrue($stub->liked(1));
        $this->assertFalse($stub->liked(2));

        // User 2 toggles like
        $stub->likeToggle(2);
        $stub->refresh();
        $this->assertEquals(2, $stub->likes_count);
        $this->assertTrue($stub->liked(1));
        $this->assertTrue($stub->liked(2));

        // User 1 toggles off
        $stub->likeToggle(1);
        $stub->refresh();
        $this->assertEquals(1, $stub->likes_count);
        $this->assertFalse($stub->liked(1));
        $this->assertTrue($stub->liked(2));
    }
} 