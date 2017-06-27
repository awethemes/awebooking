<?php
namespace AweBooking\Admin\Meta_Boxes;

use Skeleton\Metabox;

abstract class Meta_Boxes_Abstract {
	/**
	 * Post type ID to register meta-boxes.
	 *
	 * @var string
	 */
	protected $post_type = 'post';

	/**
	 * Collect of meta-boxes.
	 *
	 * @var array
	 */
	protected $metaboxes = array();

	/**
	 * Constructor of class.
	 */
	public function __construct() {
		add_action( 'save_post_' . $this->post_type, [ $this, 'doing_save' ], 10, 3 );
		add_action( 'awebooking/register_admin_scripts', [ $this, 'admin_enqueue_scripts' ] );
	}

	/**
	 * Enqueue scripts only in this CPT.
	 *
	 * @access private
	 *
	 * @return void
	 */
	public function admin_scripts() {}

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
	 * Make a new metabox.
	 *
	 * @param string $cmb_id Metabox ID.
	 */
	public function create_metabox( $cmb_id, array $args = [] ) {
		$this->metaboxes[ $cmb_id ] = new Metabox( $cmb_id, array(
			'object_types' => array( $this->post_type ),
		));

		if ( ! empty( $args ) ) {
			$this->metaboxes[ $cmb_id ]->set( $args );
		}

		return $this->metaboxes[ $cmb_id ];
	}

	/**
	 * Get a meta-box object by ID.
	 *
	 * @param  string $cmb_id The meta-box ID.
	 * @return Metabox|false
	 */
	public function get_metabox( $cmb_id ) {
		return isset( $this->metaboxes[ $cmb_id ] ) ?
			$this->metaboxes[ $cmb_id ] :
			false;
	}

	/**
	 * Get all meta-boxes.
	 *
	 * @return array
	 */
	public function get_metaboxes() {
		return $this->metaboxes;
	}

	/**
	 * Handler enqueue admin scripts.
	 *
	 * @access private
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		if ( ! $this->check_current_screen() ) {
			return;
		}

		$this->admin_scripts();
	}

	/**
	 * Check current screen in current CPT.
	 *
	 * @return boolean
	 */
	protected function check_current_screen() {
		$wp_screen = get_current_screen();

		// Prevent on edit.php.
		if ( 'edit' === $wp_screen->base || ! empty( $wp_screen->taxonomy ) ) {
			return false;
		}

		return ( $wp_screen && $wp_screen->post_type === $this->post_type );
	}
}
