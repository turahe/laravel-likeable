<?php

namespace Turahe\Tests\Likeable;

use Turahe\Likeable\Models\Like;
use Turahe\Likeable\Enums\LikeType;
use Illuminate\Support\Facades\Schema;
use Turahe\Tests\Likeable\Models\Stub;
use Illuminate\Database\Eloquent\Model;
use Turahe\Likeable\Events\ModelWasLiked;
use Turahe\Likeable\Events\ModelWasUnliked;
use Turahe\Likeable\Events\ModelWasDisliked;
use Turahe\Likeable\Events\ModelWasUndisliked;
use Illuminate\Support\Facades\Event;

class EventTest extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Model::unguard();
        Event::fake();
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
    public function test_model_was_liked_event()
    {
        $stub = Stub::create(['name'=>123]);

        $stub->like(1);

        Event::assertDispatched(ModelWasLiked::class, function ($event) use ($stub) {
            return $event->likeable->id === $stub->id &&
                   $event->likeable instanceof Stub &&
                   $event->userId === 1;
        });
    }

    /**
     * @runInSeparateProcess
     */
    public function test_model_was_unliked_event()
    {
        $stub = Stub::create(['name'=>123]);
        $stub->like(1);

        $stub->unlike(1);

        Event::assertDispatched(ModelWasUnliked::class, function ($event) use ($stub) {
            return $event->likeable->id === $stub->id &&
                   $event->likeable instanceof Stub &&
                   $event->userId === 1;
        });
    }

    /**
     * @runInSeparateProcess
     */
    public function test_model_was_disliked_event()
    {
        $stub = Stub::create(['name'=>123]);

        $stub->dislike(1);

        Event::assertDispatched(ModelWasDisliked::class, function ($event) use ($stub) {
            return $event->likeable->id === $stub->id &&
                   $event->likeable instanceof Stub &&
                   $event->userId === 1;
        });
    }

    /**
     * @runInSeparateProcess
     */
    public function test_model_was_undisliked_event()
    {
        $stub = Stub::create(['name'=>123]);
        $stub->dislike(1);

        $stub->undislike(1);

        Event::assertDispatched(ModelWasUndisliked::class, function ($event) use ($stub) {
            return $event->likeable->id === $stub->id &&
                   $event->likeable instanceof Stub &&
                   $event->userId === 1;
        });
    }

    /**
     * @runInSeparateProcess
     */
    public function test_toggle_like_fires_correct_events()
    {
        $stub = Stub::create(['name'=>123]);

        // First toggle should fire ModelWasLiked
        $stub->likeToggle(1);
        Event::assertDispatched(ModelWasLiked::class);
        Event::assertNotDispatched(ModelWasUnliked::class);

        // Reset events
        Event::fake();

        // Second toggle should fire ModelWasUnliked
        $stub->likeToggle(1);
        Event::assertDispatched(ModelWasUnliked::class);
        Event::assertNotDispatched(ModelWasLiked::class);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_toggle_dislike_fires_correct_events()
    {
        $stub = Stub::create(['name'=>123]);

        // First toggle should fire ModelWasDisliked
        $stub->dislikeToggle(1);
        Event::assertDispatched(ModelWasDisliked::class);
        Event::assertNotDispatched(ModelWasUndisliked::class);

        // Reset events
        Event::fake();

        // Second toggle should fire ModelWasUndisliked
        $stub->dislikeToggle(1);
        Event::assertDispatched(ModelWasUndisliked::class);
        Event::assertNotDispatched(ModelWasDisliked::class);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_multiple_likes_fire_multiple_events()
    {
        $stub = Stub::create(['name'=>123]);

        $stub->like(1);
        $stub->like(2);
        $stub->like(3);

        Event::assertDispatchedTimes(ModelWasLiked::class, 3);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_multiple_dislikes_fire_multiple_events()
    {
        $stub = Stub::create(['name'=>123]);

        $stub->dislike(1);
        $stub->dislike(2);
        $stub->dislike(3);

        Event::assertDispatchedTimes(ModelWasDisliked::class, 3);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_duplicate_like_does_not_fire_event()
    {
        $stub = Stub::create(['name'=>123]);

        $stub->like(1);
        Event::assertDispatchedTimes(ModelWasLiked::class, 1);

        // Try to like again - should not fire event
        $stub->like(1);
        Event::assertDispatchedTimes(ModelWasLiked::class, 1);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_duplicate_dislike_does_not_fire_event()
    {
        $stub = Stub::create(['name'=>123]);

        $stub->dislike(1);
        Event::assertDispatchedTimes(ModelWasDisliked::class, 1);

        // Try to dislike again - should not fire event
        $stub->dislike(1);
        Event::assertDispatchedTimes(ModelWasDisliked::class, 1);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_unlike_without_like_does_not_fire_event()
    {
        $stub = Stub::create(['name'=>123]);

        $stub->unlike(1);

        Event::assertNotDispatched(ModelWasUnliked::class);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_undislike_without_dislike_does_not_fire_event()
    {
        $stub = Stub::create(['name'=>123]);

        $stub->undislike(1);

        Event::assertNotDispatched(ModelWasUndisliked::class);
    }
} 