<?php

namespace Turahe\Tests\Likeable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Turahe\Likeable\Models\Like;
use Turahe\Tests\Likeable\Models\Stub;

class DislikeTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Model::unguard();
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        Schema::create('books', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('users', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::drop('books');
        Schema::drop('users');
    }

    /**
     * @runInSeparateProcess
     */
    public function test_basic_dislike()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name' => 123]);

        $stub->dislike(1);

        $this->assertEquals(1, $stub->dislikes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_multiple_dislikes()
    {
        $stub = Stub::create(['name' => 123]);

        $stub->dislike(1);
        $stub->dislike(2);
        $stub->dislike(3);
        $stub->dislike(4);

        $this->assertEquals(4, $stub->dislikes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_undislike()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name' => 123]);

        $stub->dislike(1);
        $this->assertEquals(1, $stub->dislikes_count);

        $stub->undislike(1);
        $stub->refresh();
        $this->assertEquals(0, $stub->dislikes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_dislike_toggle()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name' => 123]);

        // First toggle should add dislike
        $stub->dislikeToggle(1);
        $this->assertEquals(1, $stub->dislikes_count);
        $this->assertTrue($stub->disliked(1));

        // Second toggle should remove dislike
        $stub->dislikeToggle(1);
        $stub->refresh();
        $this->assertEquals(0, $stub->dislikes_count);
        $this->assertFalse($stub->disliked(1));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_disliked_method()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name' => 123]);

        $this->assertFalse($stub->disliked(1));

        $stub->dislike(1);
        $this->assertTrue($stub->disliked(1));

        $stub->undislike(1);
        $stub->refresh();
        $this->assertFalse($stub->disliked(1));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_get_disliked_attribute()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name' => 123]);

        $this->assertFalse($stub->disliked(1));

        $stub->dislike(1);
        $this->assertTrue($stub->disliked(1));

        $stub->undislike(1);
        $stub->refresh();
        $this->assertFalse($stub->disliked(1));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_where_disliked_by()
    {
        Stub::create(['name' => 'A'])->dislike(1);
        Stub::create(['name' => 'B'])->dislike(1);
        Stub::create(['name' => 'C'])->dislike(1);

        $stubs = Stub::whereDislikedBy(1)->get();
        $shouldBeEmpty = Stub::whereDislikedBy(2)->get();

        $this->assertEquals(3, $stubs->count());
        $this->assertEmpty($shouldBeEmpty);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_remove_dislikes()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name' => 123]);

        $stub->dislike(1);
        $stub->dislike(2);
        $stub->dislike(3);

        $this->assertEquals(3, $stub->dislikes_count);

        $stub->removeDislikes();
        $stub->refresh();

        $this->assertEquals(0, $stub->dislikes_count);
        $this->assertEquals(0, Like::where('likeable_id', $stub->id)->count());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_likes_diff_dislikes_count()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name' => 123]);

        $stub->like(1);
        $stub->like(2);
        $stub->like(3);
        $stub->dislike(4);
        $stub->dislike(5);
        $stub->refresh();

        // 3 likes - 2 dislikes = 1
        $this->assertEquals(1, $stub->likes_diff_dislikes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_order_by_dislikes_count()
    {
        $stub1 = Stub::create(['name' => 'A']);
        $stub2 = Stub::create(['name' => 'B']);
        $stub3 = Stub::create(['name' => 'C']);

        $stub1->dislike(1);
        $stub1->dislike(2);
        $stub2->dislike(1);
        $stub3->dislike(1);
        $stub3->dislike(2);
        $stub3->dislike(3);

        $ordered = Stub::orderByDislikesCount('desc')->get();

        $this->assertEquals($stub3->id, $ordered->first()->id); // 3 dislikes
        $this->assertEquals($stub1->id, $ordered->get(1)->id);  // 2 dislikes
        $this->assertEquals($stub2->id, $ordered->last()->id);  // 1 dislike
    }
}
