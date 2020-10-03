<?php

namespace Turahe\Likeable;

use Illuminate\Support\ServiceProvider;

class LikeableServiceProvider extends ServiceProvider
{
	public function boot()
	{
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
	}

	public function register() {}
}
