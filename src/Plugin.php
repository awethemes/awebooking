<?php

namespace AweBooking\PMS;

use AweBooking\Features\GutenbergEverywhere\GutenbergEverywhere;
use AweBooking\Models\Post;
use AweBooking\System\Container;
use AweBooking\System\FulltextSearch\Engines\EngineFactory;
use AweBooking\System\FulltextSearch\FulltextSearch;

final class Plugin
{
	use Traits\SingletonTrait;

	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * Constructor.
	 */
	private function __construct()
	{
		$this->container = new Container();

		$this->container->boot();
	}

	/**
	 * @return Container
	 */
	public function getContainer()
	{
		return $this->container;
	}

	/**
	 * Initialize plugin functionality.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerBindings();

		// REST API endpoints.
		// High priority so it runs after create_initial_rest_routes().
		add_action('rest_api_init', [$this, 'registerRestRoutes'], 100);

		// Init features.
		$this->container->call(GutenbergEverywhere::class, [], 'init');

		// Init logic hooks.
		// $this->container->call(LogicHooks::class));

		// Front-end hooks.
		// add_action(
		//     'after_setup_theme',
		//     [$this->getContainer()->make(FrontendPages::class), 'init']
		// );

		// Admin hooks.
		if (is_admin()) {
			// $this->getContainer()->make(AdminMenu::class)->init();
		}
	}

	/**
	 * Register component bindings.
	 *
	 * @return void
	 */
	protected function registerBindings()
	{
		// $this->container->singleton(
		//     Contracts\ArtistRepository::class,
		//     Repositories\EloquentArtistRepository::class
		// );

		$this->container->singleton(FulltextSearch::class, function () {
			$search = new FulltextSearch(
				EngineFactory::createMeiliSearchEngine()
			);

			$search->register('posts', Post::class);

			return $search;
		});
	}

	/**
	 * Registers REST API routes.
	 *
	 * @return void
	 */
	public function registerRestRoutes()
	{
		$controllers = [
			REST\Controllers\Admin\MenuItemsController::class,
		];

		foreach ($controllers as $class) {
			$controller = $this->container->make($class);
			$controller->register_routes();
		}
	}
}
