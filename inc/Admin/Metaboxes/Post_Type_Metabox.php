<?php
namespace AweBooking\Admin\Metaboxes;

use Skeleton\Metabox;

abstract class Post_Type_Metabox {
	/**
	 * Post type ID to register meta-boxes.
	 *
	 * @var string
	 */
	protected $post_type = 'post';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->register();
		add_action( 'save_post_' . $this->post_type, [ $this, 'doing_save' ], 10, 3 );
	}

	/**
	 * Here you can register metaboxes.
	 *
	 * @return void
	 */
	public function register() {}

	/**
	 * Save CPT metadata when a custom post is saved.
	 *
	 * @access private
	 *
	 * @param int  $post_id The post ID.
	 * @param post $post    The post object.
	 * @param bool $update  Whether this is an existing post being updated or not.
	 */
	public function doing_save( $post_id, $post, $update ) {}

	/**
	 * Create a new metabox.
	 *
	 * @param string $cmb_id Metabox ID.
	 * @param array  $args   Metabox args.
	 */
	protected function create_metabox( $cmb_id, array $args = [] ) {
		$metabox = new Metabox( $cmb_id, array(
			'object_types' => array( $this->post_type ),
		));

		if ( ! empty( $args ) ) {
			$metabox->set( $args );
		}

		return $metabox;
	}

	/**
	 * Check current screen in current CPT.
	 *
	 * @return bool
	 */
	protected function is_current_screen() {
		$wp_screen = get_current_screen();

		// Prevent on edit.php.
		if ( 'edit' === $wp_screen->base || ! empty( $wp_screen->taxonomy ) ) {
			return false;
		}

		return ( $wp_screen && $wp_screen->post_type === $this->post_type );
	}
}
