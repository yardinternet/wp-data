<?php

declare(strict_types=1);

namespace Yard\Data\Providers;

use Illuminate\Support\ServiceProvider;
use Yard\Data\CommentData;
use Yard\Data\PostData;
use Yard\Data\TermData;
use Yard\Data\UserData;

class DataServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__.'/../../config/yard-data.php',
			'yard-data'
		);
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__.'/../../config/yard-data.php' => $this->app->configPath('yard-data.php'),
		], 'config');

		$this->registerNonPersistentCacheGroups();
	}

	protected function registerNonPersistentCacheGroups(): void
	{
		add_action('init', function () {
			wp_cache_add_non_persistent_groups([
				PostData::CACHE_GROUP,
				TermData::CACHE_GROUP,
				UserData::CACHE_GROUP,
				CommentData::CACHE_GROUP,
			]);
		});
	}
}
