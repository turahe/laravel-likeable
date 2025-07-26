<?php

namespace Turahe\Tests\Likeable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Turahe\Likeable\Models\Like;
use Turahe\Likeable\Models\LikeCounter;
use Turahe\Tests\Likeable\Models\Stub;

class CommonUseTest extends BaseTestCase
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
    public function test_basic_like()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name' => 123]);

        $stub->like(1);

        $this->assertEquals(1, $stub->likes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_multiple_likes()
    {
        $stub = Stub::create(['name' => 123]);

        $stub->like(1);
        $stub->like(2);
        $stub->like(3);
        $stub->like(4);

        $this->assertEquals(4, $stub->likes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_unlike()
    {
        /** @var Stub $stub */
        $stub = Stub::create(['name' => 123]);

        $stub->unlike(1);

        $this->assertEquals(0, $stub->likes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_where_liked_by()
    {
        Stub::create(['name' => 'A'])->like(1);
        Stub::create(['name' => 'B'])->like(1);
        Stub::create(['name' => 'C'])->like(1);

        $stubs = Stub::whereLikedBy(1)->get();
        $shouldBeEmpty = Stub::whereLikedBy(2)->get();

        $this->assertEquals(3, $stubs->count());
        $this->assertEmpty($shouldBeEmpty);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_delete_model_deletes_likes()
    {
        /** @var Stub $stub1 */
        $stub1 = Stub::create(['name' => 456]);
        /** @var Stub $stub2 */
        $stub2 = Stub::create(['name' => 123]);
        /** @var Stub $stub3 */
        $stub3 = Stub::create(['name' => 888]);

        $stub1->like(1);
        $stub1->like(7);
        $stub1->like(8);
        $stub2->like(1);
        $stub2->like(2);
        $stub2->like(3);
        $stub2->like(4);

        $stub3->delete();
        $this->assertEquals(7, Like::count());
        $this->assertEquals(2, LikeCounter::count());

        $stub1->delete();
        $this->assertEquals(4, Like::count());
        $this->assertEquals(1, LikeCounter::count());

        $stub2->delete();
        $this->assertEquals(0, Like::count());
        $this->assertEquals(0, LikeCounter::count());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_rebuild_test()
    {
        $stub1 = Stub::create(['name' => 456]);
        $stub2 = Stub::create(['name' => 123]);

        $stub1->like(1);
        $stub1->like(7);
        $stub1->like(8);
        $stub2->like(1);
        $stub2->like(2);
        $stub2->like(3);
        $stub2->like(4);

        LikeCounter::truncate();

        LikeCounter::rebuild(Stub::class);

        $this->assertEquals(2, LikeCounter::count());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_order_by_likes_count()
    {
        $stub1 = Stub::create(['name' => 'A']);
        $stub2 = Stub::create(['name' => 'B']);
        $stub3 = Stub::create(['name' => 'C']);

        $stub1->like(1);
        $stub2->like(1);
        $stub2->like(2);
        $stub3->like(1);
        $stub3->like(2);
        $stub3->like(3);

        $ordered = Stub::orderByLikesCount('desc')->get();

        $this->assertEquals($stub3->id, $ordered->first()->id); // 3 likes
        $this->assertEquals($stub2->id, $ordered->get(1)->id);  // 2 likes
        $this->assertEquals($stub1->id, $ordered->last()->id);  // 1 like
    }

    /**
     * @runInSeparateProcess
     */
    public function test_collect_likers()
    {
        // Create user records in the database
        \DB::table('users')->insert([
            ['id' => 1, 'name' => 'User 1'],
            ['id' => 2, 'name' => 'User 2'],
            ['id' => 3, 'name' => 'User 3'],
        ]);

        $stub = Stub::create(['name' => 123]);

        $stub->like(1);
        $stub->like(2);
        $stub->like(3);

        $likers = $stub->collectLikers();

        $this->assertCount(3, $likers);
        $this->assertEquals(1, $likers->first()->id);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_collect_dislikers()
    {
        // Create user records in the database
        \DB::table('users')->insert([
            ['id' => 1, 'name' => 'User 1'],
            ['id' => 2, 'name' => 'User 2'],
            ['id' => 3, 'name' => 'User 3'],
        ]);

        $stub = Stub::create(['name' => 123]);

        $stub->dislike(1);
        $stub->dislike(2);
        $stub->dislike(3);

        $dislikers = $stub->collectDislikers();

        $this->assertCount(3, $dislikers);
        $this->assertEquals(1, $dislikers->first()->id);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_likes_and_dislikes()
    {
        $stub = Stub::create(['name' => 123]);

        $stub->like(1);
        $stub->like(2);
        $stub->dislike(3);
        $stub->dislike(4);

        $likesAndDislikes = $stub->likesAndDislikes();

        $this->assertCount(4, $likesAndDislikes->get());
    }

    /**
     * @runInSeparateProcess
     */
    public function test_get_liked_attribute()
    {
        $stub = Stub::create(['name' => 123]);

        $this->assertFalse($stub->liked(1));

        $stub->like(1);
        $this->assertTrue($stub->liked(1));

        $stub->unlike(1);
        $this->assertFalse($stub->liked(1));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_zero_user_id()
    {
        $stub = Stub::create(['name' => 123]);

        // Test that user ID 0 is valid
        $stub->like(0);
        $this->assertEquals(1, $stub->likes_count);
        $this->assertTrue($stub->liked(0));

        $stub->unlike(0);
        $this->assertEquals(0, $stub->likes_count);
        $this->assertFalse($stub->liked(0));
    }
}
