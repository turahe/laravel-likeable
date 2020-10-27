<?php

namespace Turahe\Likeable;

use Turahe\Likeable\Console\LikeableRecountCommand;
use Turahe\Likeable\Contracts\Like as LikeContract;
use Turahe\Likeable\Contracts\LikeableService as LikeableServiceContract;
use Turahe\Likeable\Contracts\LikeCounter as LikeCounterContract;
use Turahe\Likeable\Models\Like;
use Turahe\Likeable\Models\LikeCounter;
use Turahe\Likeable\Observers\LikeObserver;
use Turahe\Likeable\Services\LikeableService;
use Illuminate\Support\ServiceProvider;

class LikeableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        $this->registerConsoleCommands();
        $this->registerObservers();
        $this->registerPublishes();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerContracts();
    }

    /**
     * Register Likeable's models observers.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function registerObservers()
    {
        $this->app->make(LikeContract::class)->observe(LikeObserver::class);
    }

    /**
     * Register Likeable's console commands.
     *
     * @return void
     */
    protected function registerConsoleCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                LikeableRecountCommand::class,
            ]);
        }
    }

    /**
     * Register Likeable's classes in the container.
     *
     * @return void
     */
    protected function registerContracts()
    {
        $this->app->bind(LikeContract::class, Like::class);
        $this->app->bind(LikeCounterContract::class, LikeCounter::class);
        $this->app->singleton(LikeableServiceContract::class, LikeableService::class);
    }

    /**
     * Setup the resource publishing groups for Likeable.
     *
     * @return void
     */
    protected function registerPublishes()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../database/migrations' => database_path('migrations'),
            ], 'migrations');
        }
    }
}
