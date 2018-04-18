<?php
namespace AweBooking\Providers;

use Awethemes\Http\Request;
use AweBooking\Component\Http\Kernel;
use AweBooking\Component\Http\Exceptions\ModelNotFoundException;
use AweBooking\Component\Routing\Redirector;
use AweBooking\Component\Routing\Url_Generator;
use AweBooking\Component\Routing\Binding_Resolver;
use AweBooking\Support\Service_Provider;

class Http_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the plugin.
	 *
	 * @access private
	 */
	public function register() {
		$this->binding_request();
		$this->binding_url_generator();
		$this->binding_redirector();
		$this->binding_kernel();
	}

	/**
	 * Init service provider.
	 *
	 * @return void
	 */
	public function init() {
		$this->setup_router_binding();

		add_action( 'parse_request', [ $this, 'dispatch' ], 1 );
		add_action( 'current_screen', [ $this, 'admin_dispatch' ], 1 );
	}

	/**
	 * Binding the http request.
	 *
	 * @return void
	 */
	protected function binding_request() {
		$this->plugin->bind( 'request', function() {
			$request = Request::capture();

			$request->set_wp_session( $this->plugin['session']->get_store() );

			return $request;
		});

		$this->plugin->alias( 'request', Request::class );
	}

	/**
	 * Binding the url_generator.
	 *
	 * @return void
	 */
	protected function binding_url_generator() {
		$this->plugin->singleton( 'url', function() {
			return new Url_Generator( $this->plugin );
		});

		$this->plugin->alias( 'url', Url_Generator::class );
	}

	/**
	 * Binding the redirector.
	 *
	 * @return void
	 */
	protected function binding_redirector() {
		$this->plugin->singleton( 'redirector', function() {
			$redirector = new Redirector( $this->plugin->make( Url_Generator::class ) );

			$redirector->set_request( $this->plugin->make( 'request' ) );
			$redirector->set_wp_session( $this->plugin['session']->get_store() );

			return $redirector;
		});

		$this->plugin->alias( 'redirector', Redirector::class );
	}

	/**
	 * Binding the Kernel.
	 *
	 * @return void
	 */
	protected function binding_kernel() {
		$this->plugin->singleton( 'route_binder', function() {
			return new Binding_Resolver( $this->plugin );
		});

		$this->plugin->bind( 'kernel', function() {
			return new Kernel( $this->plugin );
		});
	}

	/**
	 * Setup router binding.
	 *
	 * @return void
	 */
	protected function setup_router_binding() {
		$binder = $this->plugin->make( 'route_binder' );

		// Core models to binding.
		$models_binding = apply_filters( 'awebooking/route_models_binding', [
			'booking'      => \AweBooking\Model\Booking::class,
			'room_item'    => \AweBooking\Model\Booking\Room_Item::class,
			'payment_item' => \AweBooking\Model\Booking\Payment_Item::class,
			'room_type'    => \AweBooking\Model\Room_Type::class,
			'room'         => \AweBooking\Model\Room::class,
		]);

		foreach ( $models_binding as $key => $model ) {
			$binder->model( $key, $model );
		}

		/**
		 * Fire action for user setup custom route binding.
		 *
		 * @param \AweBooking\Http\Routing\Binding_Resolver $binder The binder.
		 */
		do_action( 'awebooking/route_binding', $binder );
	}

	/**
	 * Dispatch the incoming request (on front-end).
	 *
	 * @access private
	 */
	public function dispatch() {
		global $wp;

		// Leave if empty the request.
		if ( empty( $wp->query_vars['awebooking_route'] ) ) {
			return;
		}

		// Handle the awebooking_route endpoint requests.
		$this->plugin->make( 'kernel' )
			->use_request_uri( $wp->query_vars['awebooking_route'] )
			->handle( $this->plugin->make( 'request' ) );
	}

	/**
	 * Dispatch the incoming request (on admin).
	 *
	 * @param  WP_Screen $current_screen Current WP_Screen.
	 * @access private
	 */
	public function admin_dispatch( $current_screen ) {
		if ( defined( 'DOING_AJAX' ) || isset( $_GET['page'] ) || empty( $_REQUEST['awebooking'] ) ) {
			return;
		}

		// Get the request uri.
		$request_uri = '/' . trim( rawurldecode( $_REQUEST['awebooking'] ), '/' );

		// Set the screen ID.
		$current_screen->base = 'awebooking_route';
		$current_screen->id   = 'awebooking' . $request_uri;

		// Thos action is not needed.
		remove_action( 'admin_head', 'wp_admin_canonical_url' );

		// Handle the route.
		$this->plugin->make( 'kernel' )
			->use_request_uri( $request_uri )
			->handle( $this->plugin->make( 'request' ) );
	}
}
