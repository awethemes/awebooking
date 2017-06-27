<?php
namespace AweBooking\Admin\Meta_Boxes;

use AweBooking\Room;
use AweBooking\Room_Type;

class Room_Type_Meta_Boxes extends Meta_Boxes_Abstract {
	/**
	 * Post type ID to register meta-boxes.
	 *
	 * @var string
	 */
	protected $post_type = 'room_type';

	/**
	 * Constructor of class.
	 */
	public function __construct() {
		parent::__construct();

		$this->register_main_metabox();
		$this->register_gallery_metabox();
		$this->register_description_metabox();

		add_action( 'edit_form_top', [ $this, 'setup_room_type' ] );
	}

	/**
	 * Setup room type object.
	 *
	 * @access private
	 * @see wp-admin/edit-form-advanced.php
	 *
	 * @param WP_Post $post Post object.
	 */
	public function setup_room_type( $post ) {
		// If this isn't a 'room_type', do nothing.
		if ( ! $this->check_current_screen() ) {
			return;
		}

		global $room_type;

		$room_type = new Room_Type( $post->ID );
	}

	/**
	 * Enqueue scripts only in this CPT.
	 *
	 * @access private
	 *
	 * @return void
	 */
	public function admin_scripts() {
		wp_enqueue_script( 'awebooking-room-type-meta-boxes' );
	}

	/**
	 * Save CPT metadata when a custom post is saved.
	 *
	 * @access private
	 *
	 * @param int  $post_id The post ID.
	 * @param post $post    The post object.
	 * @param bool $update  Whether this is an existing post being updated or not.
	 */
	public function doing_save( $post_id, $post, $update ) {
		if ( isset( $_POST['abkng_rooms'] ) && is_array( $_POST['abkng_rooms'] ) ) {
			$request_rooms = wp_unslash( $_POST['abkng_rooms'] );
			awebooking()->make( 'store.room_type' )->bulk_sync_rooms( $post_id, $request_rooms );
		}
	}

	/**
	 * Register gallery metabox.
	 *
	 * @access private
	 *
	 * @return void
	 */
	public function register_gallery_metabox() {
		$metabox = $this->create_metabox( 'room_type_gallery', [
			'title'    => esc_html__( 'Room Type Gallery', 'awebooking' ),
			'context'  => 'side',
			'priority' => 'default',
		]);

		$metabox->add_field([
			'id'         => 'gallery',
			'type'       => 'file_list',
			'query_args' => [ 'type' => 'image' ],
			'text'       => [
				'add_upload_files_text' => esc_html__( 'Set room type gallery', 'awebooking' ),
			],
		]);
	}

	/**
	 * Register description metabox.
	 *
	 * @access private
	 *
	 * @return void
	 */
	public function register_description_metabox() {
		$metabox = $this->create_metabox( 'room_type_description', [
			'title'    => esc_html__( 'Description', 'awebooking' ),
			'context'  => 'advanced',
			'priority' => 'default',
		]);

		$metabox->add_field( array(
			'id'      => 'short_description',
			'type'    => 'wysiwyg',
			'options' => array(
				// 'tinymce'       => false,
				// 'media_buttons' => false,
				'textarea_rows' => 7,
			),
		));
	}

	/**
	 * Add meta boxes to this post type.
	 *
	 * @access private
	 *
	 * @return void
	 */
	public function register_main_metabox() {
		$metabox = $this->create_metabox( 'room_type', [
			'title'         => esc_html__( 'Room Type Data', 'awebooking' ),
			'context'       => 'advanced',
			'priority'      => 'low',
			'vertical_tabs' => true,
		]);

		$currency = awebooking()->make( 'currency' );

		// Register Metabox Sections.
		$general = $metabox->add_section( 'general', array(
			'title' => esc_html__( 'General', 'awebooking' ),
		));

		$general->add_field( array(
			'id'         => 'base_price',
			'type'       => 'text_small',
			'name'       => sprintf( esc_html__( 'Starting price (%s)', 'awebooking' ), esc_html( $currency->get_symbol() ) ),
			'validate'   => 'required|numeric:min:0',
			'sanitization_cb' => 'abkng_sanitize_price',
			'before'     => '<div class="skeleton-input-group">',
			'after'      => '<span class="skeleton-input-group__addon">per night</span></div>',
		));

		$general->add_field( array(
			'id'         => 'number_adults',
			'type'       => 'text_small',
			'name'       => esc_html__( 'And capacity for', 'awebooking' ),
			'default'    => 2,
			'sanitization_cb' => 'absint',
			'validate'   => 'required|numeric|min:0',
			'render_field_cb'   => array( $this, '_room_field_callback' ),
		));

		$general->add_field( array(
			'id'         => 'number_children',
			'type'       => 'text_small',
			'name'       => esc_html__( 'Number children', 'awebooking' ),
			'default'    => 2,
			'sanitization_cb' => 'absint',
			'validate'   => 'required|numeric|min:0',
			'show_on_cb' => '__return_false', // NOTE: We'll handler display in "number_adults".
		));

		$general->add_field( array(
			'id'         => 'max_adults',
			'type'       => 'text_small',
			'name'       => esc_html__( 'Allow extra capacity for', 'awebooking' ),
			'default'    => 0,
			'sanitization_cb' => 'absint',
			'validate'   => 'required|numeric|min:0',
			'render_field_cb'   => array( $this, '_room_max_field_callback' ),
		));

		$general->add_field( array(
			'id'         => 'max_children',
			'type'       => 'text_small',
			'name'       => esc_html__( 'Number children', 'awebooking' ),
			'default'    => 0,
			'validate'   => 'required|numeric|min:0',
			'sanitization_cb' => 'absint',
			'show_on_cb' => '__return_false', // NOTE: We'll handler display in "max_number_adults".
		));

		$general->add_field( array(
			'id'         => 'minimum_night',
			'type'       => 'text_small',
			'name'       => esc_html__( 'Minimum night(s) required', 'awebooking' ),
			'default'    => 1,
			'sanitization_cb' => 'absint',
			'validate'   => 'required|numeric|min:1',
			'before'     => '<div class="skeleton-input-group">',
			'after'      => '<span class="skeleton-input-group__addon">night(s)</span></div>',
		));

		$general->add_field( array(
			'id'         => '__rooms_numbers__',
			'type'       => 'title',
			'name'       => esc_html__( 'Number of rooms', 'awebooking' ),
			'show_on_cb' => [ $this, '_render_rooms_callback' ],
		));

		$extra_service = $metabox->add_section( 'extra_service', array(
			'title' => esc_html__( 'Extra Services', 'awebooking' ),
		));

		$extra_service->add_field( array(
			'id'         => '__extra_services__',
			'type'       => 'title',
			'name'       => esc_html__( 'Extra Services', 'awebooking' ),
			'show_on_cb' => [ $this, '_render_extra_services_callback' ],
		));

		do_action( 'awebooking/register_metabox/room_type', $metabox );
	}

	/**
	 * Render rooms callback.
	 *
	 * @return void
	 */
	public function _render_rooms_callback( $field ) {
		global $room_type;

		$screen = get_current_screen();
		$is_edit = $screen->action === 'add';

		include trailingslashit( __DIR__ ) . '/views/rooms.php';
	}

	/**
	 * Render service callback.
	 *
	 * @return void
	 */
	public function _render_extra_services_callback( $field ) {
		global $room_type;

		$screen = get_current_screen();
		$is_edit = $screen->action === 'add';

		include trailingslashit( __DIR__ ) . '/views/extra-services.php';
	}

	/**
	 * Render rooms callback.
	 *
	 * @return void
	 */
	public function _room_field_callback( $field_args, $field ) {
		$cmb2 = $field->get_cmb();
		$number_children_field = $cmb2->get_field( 'number_children' );

		echo '<div class="skeleton-input-group">';
		skeleton_render_field( $field );
		echo '<span class="skeleton-input-group__addon">' . esc_html__( 'adults', 'awebooking' ) . '</span>';
		echo '</div>';

		echo '<div class="skeleton-input-group">';
		skeleton_render_field( $number_children_field );
		echo '<span class="skeleton-input-group__addon">' . esc_html__( 'children', 'awebooking' ) . '</span>';
		echo '</div>';

		skeleton_display_field_errors( $number_children_field );
	}

	/**
	 * Render rooms callback.
	 *
	 * @return void
	 */
	public function _room_max_field_callback( $field_args, $field ) {
		$cmb2 = $field->get_cmb();
		$max_children_field = $cmb2->get_field( 'max_children' );

		echo '<div class="skeleton-input-group">';
		skeleton_render_field( $field );
		echo '<span class="skeleton-input-group__addon">' . esc_html__( 'adults', 'awebooking' ) . '</span>';
		echo '</div>';

		echo '<div class="skeleton-input-group">';
		skeleton_render_field( $max_children_field );
		echo '<span class="skeleton-input-group__addon">' . esc_html__( 'children', 'awebooking' ) . '</span>';
		echo '</div>';

		skeleton_display_field_errors( $max_children_field );
	}
}
