<?php

namespace Skeleton;

use Skeleton\CMB2\CMB2;
use Skeleton\CMB2\CMB2_Hooks;
use Skeleton\CMB2\Scripts_Hooks;
use Skeleton\Walker\Walker_Hooks;
use Skeleton\Webfonts\Webfonts_Hooks;
use Skeleton\Iconfonts\Iconfonts_Hooks;
use Skeleton\Support\Multidimensional;
use Skeleton\Container\Container;

final class Skeleton extends Container {
	const VERSION = '0.1.0';

	/**
	 * All of the custom post types.
	 *
	 * @var array
	 */
	protected $post_types = array();

	/**
	 * All of the custom taxonomies.
	 *
	 * @var array
	 */
	protected $taxonomies = array();

	protected $cmb2 = array();

	/**
	 * The current globally available container (if any).
	 *
	 * @var static
	 */
	protected static $instance;

	/**
	 * Set the globally available instance of the container.
	 *
	 * @return static
	 */
	public static function get_instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * Instantiate the container.
	 *
	 * Objects and parameters can be passed as argument to the constructor.
	 *
	 * @param array $values The parameters or objects.
	 */
	public function __construct( array $values = array() ) {
		parent::__construct( $values );

		static::$instance = $this;

		define( 'SKELETON_VERSION', static::VERSION );

		$this['path'] = plugin_dir_path( __DIR__ );
		$this['url']  = plugin_dir_url( __DIR__ );
		$this['public_path'] = trailingslashit( $this['path'] . 'public' );
		$this['public_url']  = trailingslashit( $this['url'] . 'public' );

		// Register core framework hooks.
		$this->trigger( new CMB2_Hooks );
		$this->trigger( new Scripts_Hooks );
		$this->trigger( new Webfonts_Hooks );
		$this->trigger( new Walker_Hooks );
		$this->trigger( new Iconfonts_Hooks );
	}

	/**
	 * Attach a custom post type to the container.
	 *
	 * @param Post_Type $post_type Custom post type class.
	 */
	public function bind_post_type( Post_Type $post_type ) {
		// $name = $post_type->get_instance()->name;
		$this->post_types[] = $post_type;
	}

	/**
	 * Attach a custom taxonomy to the container.
	 *
	 * @param Taxonomy $taxonomy Custom taxonomy class.
	 */
	public function bind_taxonomy( Taxonomy $taxonomy ) {
		// $name = $taxonomy->get_instance()->name;
		$this->taxonomies[] = $taxonomy;
	}

	public function bind_cmb2( CMB2 $cmb2 ) {
		$this->cmb2[] = $cmb2;
	}

	/**
	 * Bootstrap the framework.
	 */
	public function boot() {
		parent::boot();

		foreach ( $this->post_types as $post_type ) {
			$post_type->register();
		}

		foreach ( $this->taxonomies as $taxonomy ) {
			$taxonomy->register();
		}
	}

	/**
	 * Run Skeleton after `cmb2_init` fired.
	 *
	 * @return void
	 */
	public function run() {
		do_action( 'skeleton/init', $this );

		$this->boot();

		do_action( 'skeleton/after_init', $this );
	}
}
