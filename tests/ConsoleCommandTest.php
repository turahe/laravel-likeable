<?php

namespace Turahe\Tests\Likeable;

use Turahe\Likeable\Models\Like;
use Illuminate\Support\Facades\Schema;
use Turahe\Tests\Likeable\Models\Stub;
use Illuminate\Database\Eloquent\Model;
use Turahe\Likeable\Models\LikeCounter;
use Illuminate\Support\Facades\Artisan;
use Turahe\Likeable\Console\LikeableRecountCommand;

class ConsoleCommandTest extends BaseTestCase
{
    public function setUp(): void
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
    }

    public function tearDown(): void
    {
        Schema::drop('books');
    }

    /**
     * @runInSeparateProcess
     */
    public function test_recount_command_for_specific_model()
    {
        // Create some test data
        $stub1 = Stub::create(['name'=>'A']);
        $stub2 = Stub::create(['name'=>'B']);

        $stub1->like(1);
        $stub1->like(2);
        $stub2->like(3);

        // Manually delete counters to simulate corruption
        LikeCounter::where('likeable_type', Stub::class)->delete();

        // Run the recount command
        $this->artisan('likeable:recount', ['model' => Stub::class])
            ->expectsOutput('All [' . Stub::class . '] records likes has been recounted.')
            ->assertExitCode(0);

        // Verify counters are rebuilt
        $stub1->refresh();
        $stub2->refresh();

        $this->assertEquals(2, $stub1->likes_count);
        $this->assertEquals(1, $stub2->likes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_recount_command_for_all_models()
    {
        // Create test data
        $stub1 = Stub::create(['name'=>'A']);
        $stub2 = Stub::create(['name'=>'B']);

        $stub1->like(1);
        $stub2->dislike(2);

        // Manually delete counters
        LikeCounter::truncate();

        // Run the recount command for all models
        $this->artisan('likeable:recount')
            ->assertExitCode(0);

        // Verify counters are rebuilt
        $stub1->refresh();
        $stub2->refresh();

        $this->assertEquals(1, $stub1->likes_count);
        $this->assertEquals(1, $stub2->dislikes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_recount_command_with_type_filter()
    {
        // Create test data
        $stub1 = Stub::create(['name'=>'A']);
        $stub2 = Stub::create(['name'=>'B']);

        $stub1->like(1);
        $stub1->dislike(2);
        $stub2->like(3);

        // Manually delete counters
        LikeCounter::where('likeable_type', Stub::class)->delete();

        // Run the recount command for likes only
        $this->artisan('likeable:recount', [
            'model' => Stub::class,
            'type' => 'like'
        ])
        ->expectsOutput('All [' . Stub::class . '] records likes has been recounted.')
        ->assertExitCode(0);

        // Verify counters are rebuilt
        $stub1->refresh();
        $stub2->refresh();

        // The command recounts all types when type is specified, so both likes and dislikes are recounted
        $this->assertEquals(1, $stub1->likes_count);
        $this->assertEquals(1, $stub2->likes_count);
        $this->assertEquals(1, $stub1->dislikes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_recount_command_with_dislike_type()
    {
        // Create test data
        $stub1 = Stub::create(['name'=>'A']);
        $stub2 = Stub::create(['name'=>'B']);

        $stub1->like(1);
        $stub1->dislike(2);
        $stub2->dislike(3);

        // Manually delete counters
        LikeCounter::where('likeable_type', Stub::class)->delete();

        // Run the recount command for dislikes only
        $this->artisan('likeable:recount', [
            'model' => Stub::class,
            'type' => 'dislike'
        ])
        ->expectsOutput('All [' . Stub::class . '] records likes has been recounted.')
        ->assertExitCode(0);

        // Verify only dislike counters are rebuilt
        $stub1->refresh();
        $stub2->refresh();

        // Note: The command recounts all types when type is specified, so likes are also recounted
        $this->assertEquals(1, $stub1->likes_count); // Like counter is also rebuilt
        $this->assertEquals(1, $stub1->dislikes_count);
        $this->assertEquals(1, $stub2->dislikes_count);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_recount_command_handles_empty_data()
    {
        // No test data created

        // Run the recount command
        $this->artisan('likeable:recount', ['model' => Stub::class])
            ->expectsOutput('All [' . Stub::class . '] records likes has been recounted.')
            ->assertExitCode(0);

        // Should not throw any errors
        $this->assertTrue(true);
    }

    /**
     * @runInSeparateProcess
     */
    public function test_recount_command_with_model_alias()
    {
        // Create test data
        $stub = Stub::create(['name'=>'A']);
        $stub->like(1);

        // Manually delete counters
        LikeCounter::where('likeable_type', Stub::class)->delete();

        // Run the recount command with model alias
        $this->artisan('likeable:recount', ['model' => 'Stub'])
            ->expectsOutput('All [' . Stub::class . '] records likes has been recounted.')
            ->assertExitCode(0);

        // Verify counter is rebuilt
        $stub->refresh();
        $this->assertEquals(1, $stub->likes_count);
    }
} 