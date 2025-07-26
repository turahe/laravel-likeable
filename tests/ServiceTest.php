<?php

namespace Turahe\Tests\Likeable;

use Turahe\Likeable\Models\Like;
use Turahe\Likeable\Enums\LikeType;
use Illuminate\Support\Facades\Schema;
use Turahe\Tests\Likeable\Models\Stub;
use Illuminate\Database\Eloquent\Model;
use Turahe\Likeable\Models\LikeCounter;
use Turahe\Likeable\Services\LikeableService;
use Turahe\Likeable\Exceptions\LikerNotDefinedException;
use Turahe\Likeable\Exceptions\LikeTypeInvalidException;

class ServiceTest extends BaseTestCase
{
    protected $service;

    public function setUp(): void
    {
        parent::setUp();

        Model::unguard();
        $this->service = app(LikeableService::class);
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

    public function tearDown(): void
    {
        Schema::drop('books');
        Schema::drop('users');
    }

    /**
     * @runInSeparateProcess
     */
    public function test_add_like_to()
    {
        $stub = Stub::create(['name'=>123]);

        $this->service->addLikeTo($stub, LikeType::LIKE, 1);

        $this->assertEquals(1, $stub->likes_count);
        $this->assertTrue($stub->liked(1));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_add_dislike_to()
    {
        $stub = Stub::create(['name'=>123]);

        $this->service->addLikeTo($stub, LikeType::DISLIKE, 1);

        $this->assertEquals(1, $stub->dislikes_count);
        $this->assertTrue($stub->disliked(1));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_remove_like_from()
    {
        $stub = Stub::create(['name'=>123]);
        $stub->like(1);

        $this->service->removeLikeFrom($stub, LikeType::LIKE, 1);

        $this->assertEquals(0, $stub->likes_count);
        $this->assertFalse($stub->liked(1));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_remove_dislike_from()
    {
        $stub = Stub::create(['name'=>123]);
        $stub->dislike(1);

        $this->service->removeLikeFrom($stub, LikeType::DISLIKE, 1);

        $this->assertEquals(0, $stub->dislikes_count);
        $this->assertFalse($stub->disliked(1));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_toggle_like_of()
    {
        $stub = Stub::create(['name'=>123]);

        // First toggle should add like
        $this->service->toggleLikeOf($stub, LikeType::LIKE, 1);
        $this->assertEquals(1, $stub->likes_count);
        $this->assertTrue($stub->liked(1));

        // Second toggle should remove like
        $this->service->toggleLikeOf($stub, LikeType::LIKE, 1);
        $this->assertEquals(0, $stub->likes_count);
        $this->assertFalse($stub->liked(1));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_toggle_dislike_of()
    {
        $stub = Stub::create(['name'=>123]);

        // First toggle should add dislike
        $this->service->toggleLikeOf($stub, LikeType::DISLIKE, 1);
        $this->assertEquals(1, $stub->dislikes_count);
        $this->assertTrue($stub->disliked(1));

        // Second toggle should remove dislike
        $this->service->toggleLikeOf($stub, LikeType::DISLIKE, 1);
        $this->assertEquals(0, $stub->dislikes_count);
        $this->assertFalse($stub->disliked(1));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_is_liked()
    {
        $stub = Stub::create(['name'=>123]);

        $this->assertFalse($this->service->isLiked($stub, LikeType::LIKE, 1));

        $stub->like(1);
        $this->assertTrue($this->service->isLiked($stub, LikeType::LIKE, 1));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_is_disliked()
    {
        $stub = Stub::create(['name'=>123]);

        $this->assertFalse($this->service->isLiked($stub, LikeType::DISLIKE, 1));

        $stub->dislike(1);
        $this->assertTrue($this->service->isLiked($stub, LikeType::DISLIKE, 1));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_increment_likes_count()
    {
        $stub = Stub::create(['name'=>123]);

        $this->service->incrementLikesCount($stub);

        $this->assertEquals(1, $stub->likes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_decrement_likes_count()
    {
        $stub = Stub::create(['name'=>123]);
        $stub->like(1);

        $this->service->decrementLikesCount($stub);

        $this->assertEquals(0, $stub->likes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_increment_dislikes_count()
    {
        $stub = Stub::create(['name'=>123]);

        $this->service->incrementDislikesCount($stub);

        $this->assertEquals(1, $stub->dislikes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_decrement_dislikes_count()
    {
        $stub = Stub::create(['name'=>123]);
        $stub->dislike(1);

        $this->service->decrementDislikesCount($stub);

        $this->assertEquals(0, $stub->dislikes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_remove_model_likes()
    {
        $stub = Stub::create(['name'=>123]);
        $stub->like(1);
        $stub->like(2);
        $stub->dislike(3);

        $this->service->removeModelLikes($stub, LikeType::LIKE);

        $this->assertEquals(0, $stub->likes_count);
        $this->assertEquals(1, $stub->dislikes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_remove_model_dislikes()
    {
        $stub = Stub::create(['name'=>123]);
        $stub->like(1);
        $stub->dislike(2);
        $stub->dislike(3);

        $this->service->removeModelLikes($stub, LikeType::DISLIKE);

        $this->assertEquals(1, $stub->likes_count);
        $this->assertEquals(0, $stub->dislikes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_remove_like_counters_of_type()
    {
        $stub1 = Stub::create(['name'=>'A']);
        $stub2 = Stub::create(['name'=>'B']);

        $stub1->like(1);
        $stub1->dislike(2);
        $stub2->like(3);

        $this->service->removeLikeCountersOfType(Stub::class, 'like');

        // Refresh models to get updated counts
        $stub1->refresh();
        $stub2->refresh();

        $this->assertEquals(0, $stub1->likes_count);
        $this->assertEquals(0, $stub2->likes_count);
        $this->assertEquals(1, $stub1->dislikes_count); // Should remain
    }

    /**
     * @runInSeparateProcess
     */
    public function test_fetch_likes_counters()
    {
        $stub1 = Stub::create(['name'=>'A']);
        $stub2 = Stub::create(['name'=>'B']);

        $stub1->like(1);
        $stub1->like(2);
        $stub2->like(3);

        $counters = $this->service->fetchLikesCounters(Stub::class, 'like');

        $this->assertCount(2, $counters);
        $this->assertEquals(2, $counters[0]['count']); // stub1 has 2 likes
        $this->assertEquals(1, $counters[1]['count']); // stub2 has 1 like
    }

    /**
     * @runInSeparateProcess
     */
    public function test_scope_where_liked_by()
    {
        $stub1 = Stub::create(['name'=>'A']);
        $stub2 = Stub::create(['name'=>'B']);
        $stub3 = Stub::create(['name'=>'C']);

        $stub1->like(1);
        $stub2->like(1);
        $stub3->like(2);

        $query = Stub::query();
        $this->service->scopeWhereLikedBy($query, LikeType::LIKE, 1);

        $results = $query->get();
        $this->assertCount(2, $results);
        $this->assertTrue($results->contains($stub1));
        $this->assertTrue($results->contains($stub2));
        $this->assertFalse($results->contains($stub3));
    }

    /**
     * @runInSeparateProcess
     */
    public function test_scope_order_by_likes_count()
    {
        $stub1 = Stub::create(['name'=>'A']);
        $stub2 = Stub::create(['name'=>'B']);
        $stub3 = Stub::create(['name'=>'C']);

        $stub1->like(1);
        $stub2->like(1);
        $stub2->like(2);
        $stub3->like(1);
        $stub3->like(2);
        $stub3->like(3);

        $query = Stub::query();
        $this->service->scopeOrderByLikesCount($query, LikeType::LIKE, 'desc');

        $results = $query->get();
        $this->assertEquals($stub3->id, $results->first()->id); // 3 likes
        $this->assertEquals($stub2->id, $results->get(1)->id);  // 2 likes
        $this->assertEquals($stub1->id, $results->last()->id);  // 1 like
    }

    /**
     * @runInSeparateProcess
     */
    public function test_liker_not_defined_exception()
    {
        $stub = Stub::create(['name'=>123]);

        $this->expectException(LikerNotDefinedException::class);

        $this->service->addLikeTo($stub, LikeType::LIKE, null);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_like_type_invalid_exception()
    {
        $stub = Stub::create(['name'=>123]);

        $this->expectException(LikeTypeInvalidException::class);

        // This should throw an exception because we're passing a string instead of LikeType enum
        $this->service->addLikeTo($stub, 'invalid_type', 1);
    }
} 