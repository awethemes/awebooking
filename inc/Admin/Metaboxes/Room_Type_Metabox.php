<?php
namespace AweBooking\Admin\Metaboxes;

use AweBooking\Factory;
use AweBooking\Constants;
use AweBooking\AweBooking;
use AweBooking\Model\Room_Type;
use AweBooking\Model\Room;

class Room_Type_Metabox extends Post_Type_Metabox {
	/**
	 * The main metabox instance.
	 *
	 * @var \Skeleton\CMB2\CMB2
	 */
	protected $metabox;

	/**
	 * Post type ID to register meta-boxes.
	 *
	 * @var string
	 */
	protected $post_type = 'room_type';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->metabox = $this->create_metabox( 'room_type', [
			'title'         => esc_html__( 'Room Type Data', 'awebooking' ),
			'context'       => 'normal',
			'priority'      => 'default',
			'vertical_tabs' => true,
		]);

		parent::__construct();
	}

	/**
	 * Register hooks.
	 *
	 * @access private
	 */
	public function register() {
		parent::register();

		$this->register_room_fields();
		$this->register_occupancy_fields();
		$this->register_pricing_fields();
		$this->register_services_fields();
		$this->register_amenities_fields();
		$this->register_description_fields();

		do_action( 'awebooking/register_metabox/room_type', $this->metabox );

		add_action( 'edit_form_top', [ $this, '_setup_room_type' ]);
		add_action( 'admin_menu', array( $this, '_remove_meta_box' ) );
	}

	/**
	 * Doing remove metaboxes.
	 *
	 * @access private
	 */
	public function _remove_meta_box() {
		remove_meta_box( 'hotel_amenitydiv', $this->post_type, 'side' );
		remove_meta_box( 'hotel_extra_servicediv', $this->post_type, 'side' );
	}

	/**
	 * Setup room type object.
	 *
	 * @access private
	 * @see wp-admin/edit-form-advanced.php
	 *
	 * @param WP_Post $post Post object.
	 */
	public function _setup_room_type( $post ) {
		if ( $this->is_current_screen() ) {
			global $room_type;

			$room_type = Factory::get_room_type( $post );
		}
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
		// If this is just a revision, don't do anything.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$room_type = new Room_Type( $post_id );

		if ( isset( $_POST['abkng_rooms'] ) && is_array( $_POST['abkng_rooms'] ) ) {
			$request_rooms = wp_unslash( $_POST['abkng_rooms']);
			$this->bulk_sync_rooms( $request_rooms, $room_type );
		}
	}

	/**
	 * Bulk sync rooms.
	 *
	 * @param  array       $request_rooms The request rooms.
	 * @param  Room_Type   $room_type     The room type.
	 * @return void
	 */
	public function bulk_sync_rooms( array $request_rooms, Room_Type $room_type ) {
		// Current list room of room-type.
		$db_rooms_ids = array_map( 'absint', $room_type->get_rooms()->pluck( 'id' )->all() );
		// Multilanguage need this.
		$room_type_id = apply_filters( 'awebooking/room_type/get_id_for_rooms', $room_type->get_id() );
		$touch_ids = [];
		foreach ( $request_rooms as $raw_room ) {
			// Ignore in-valid rooms from request.
			if ( ! isset( $raw_room['id'] ) || ! isset( $raw_room['name'] ) ) {
				continue;
			}
			// Sanitize data before working with database.
			$room_args = array_map( 'sanitize_text_field', $raw_room );
			if ( $room_args['id'] > 0 && in_array( (int) $room_args['id'], $db_rooms_ids ) ) {
				$room_unit = new Room( $room_args['id'] );
				$room_unit['name'] = $room_args['name'];
				$room_unit->save();
			} else {
				$room_unit = new Room;
				$room_unit['name'] = $room_args['name'];
				$room_unit['room_type'] = $room_type_id;
				$room_unit->save();
			}
			// We'll map current working ID in $touch_ids...
			if ( $room_unit->exists() ) {
				$touch_ids[] = $room_unit->get_id();
			}
		}
		// Fimally, delete invisible rooms.
		$delete_ids = array_diff( $db_rooms_ids, $touch_ids );
		if ( ! empty( $delete_ids ) ) {
			global $wpdb;
			$delete_ids = implode( ',', $delete_ids );
			// @codingStandardsIgnoreLine
			$wpdb->query( "DELETE FROM `{$wpdb->prefix}awebooking_rooms` WHERE `id` IN ({$delete_ids})" );
		}
	}

	/**
	 * Register the room fields.
	 *
	 * @return void
	 */
	protected function register_room_fields() {
		$room = $this->metabox->add_section( 'room', [
			'title' => esc_html__( 'Room', 'awebooking' ),
		]);

		$room->add_field( array(
			'id'         => '__rooms_numbers__',
			'type'       => 'title',
			'name'       => esc_html__( 'Number of rooms', 'awebooking' ),
			'show_on_cb' => function() {
				include trailingslashit( __DIR__ ) . 'views/html-room-type-rooms.php';
			},
		));
	}

	/**
	 * Register the occupancy fields.
	 *
	 * @return void
	 */
	protected function register_occupancy_fields() {
		$occupancy = $this->metabox->add_section( 'occupancy', [
			'title' => esc_html__( 'Occupancy', 'awebooking' ),
		]);

		$occupancy->add_field( [
			'id'              => '_maximum_occupancy',
			'type'            => 'text_medium',
			'name'            => esc_html__( 'Maximum occupancy', 'awebooking' ),
			'default'         => 2,
			'after'           => $this->datalist_number_callback( 1, 20 ),
			'attributes'      => [ 'list' => '_maximum_occupancy_datalist' ],
			'validate'        => 'required|numeric|min:1',
			'sanitization_cb' => 'absint',
		]);

		$sanitization_cb = function( $value, $field_args, $field ) {
			// @codingStandardsIgnoreLine
			if ( empty( $_POST['_maximum_occupancy'] ) ) {
				return 0;
			}

			$value = absint( $value );
			$maximum_occupancy = absint( $_POST['_maximum_occupancy']);

			return ( $value > $maximum_occupancy ) ? $maximum_occupancy : $value;
		};

		$occupancy->add_row( [
			'id'            => 'maximum-occupancy-row',
			'flex_columns'  => 5,
			'fields'        => [
				[
					'id'              => 'number_adults',
					'type'            => 'text',
					'name'            => esc_html__( 'Number adults', 'awebooking' ),
					'default'         => 2,
					'attributes'      => [ 'list' => 'number_adults_datalist' ],
					'after'           => $this->datalist_number_callback( 1, 20 ),
					'validate'        => 'required|numeric|min:0',
					'sanitization_cb' => $sanitization_cb,
				],
				[
					'id'              => 'number_children',
					'type'            => 'text',
					'name'            => esc_html__( 'Number children', 'awebooking' ),
					'default'         => 0,
					'attributes'      => [ 'list' => 'number_children_datalist' ],
					'after'           => $this->datalist_number_callback( 1, 20 ),
					'show_on_cb'      => [ awebooking( 'setting' ), 'is_children_bookable' ],
					'validate'        => 'required|numeric|min:0',
					'sanitization_cb' => $sanitization_cb,
				],
				[
					'id'              => 'number_infants',
					'type'            => 'text',
					'name'            => esc_html__( 'Number infants', 'awebooking' ),
					'default'         => 0,
					'attributes'      => [ 'list' => 'number_infants_datalist' ],
					'after'           => $this->datalist_number_callback( 1, 20 ),
					'show_on_cb'      => [ awebooking( 'setting' ), 'is_infants_bookable' ],
					'validate'        => 'required|numeric|min:0',
					'sanitization_cb' => $sanitization_cb,
				],
			],
		]);

		/*$occupancy->add_field( [
			'id'              => '_infants_in_calculations',
			'type'            => 'toggle',
			'desc'            => esc_html__( 'Include infants in max calculations?', 'awebooking' ),
			'default'         => true,
			'show_on_cb'      => [ awebooking( 'setting' ), 'is_infants_bookable' ],
		]);*/

		$occupancy->add_field( [
			'id'         => 'some_note',
			'type'       => 'note',
			'save_field' => false,
			'title'       => esc_html__( 'Some notes on setting capacity', 'awebooking' ),
			// @codingStandardsIgnoreLine: We already escape the output, so just using `__` here.
			'desc'       => __( "The number of adults, children etc. <b>do not</b> need to add up to the maximum occupancy. A room could sleep a maximum of 4 people, but the max adults may be 2 and max children 3. \n This would allow your guests to choose 2 adults and 2 children, or 1 adult and 3 children. (But never 2 adults and 3 children as this would exceed the max capacity.)", 'awebooking' ),
		]);
	}

	/**
	 * Register the pricing fields.
	 *
	 * @return void
	 */
	protected function register_pricing_fields() {
		$pricing = $this->metabox->add_section( 'pricing', [
			'title' => esc_html__( 'Pricing', 'awebooking' ),
		]);

		$pricing->add_field( [
			'id'   => '__standard_rate_title__',
			'type' => 'title',
			'name' => esc_html__( 'Standard rate', 'awebooking' ),
		]);

		$pricing->add_row( [
			'id'             => '_row_rate_price_',
			'flex_columns'   => 4,
			'fields'         => [
				[
					'id'              => 'base_price',
					'type'            => 'text_small',
					'name'            => esc_html__( 'Base price', 'awebooking' ),
					/* translators: %s The currency symbol */
					'append'          => sprintf( esc_html__( '%s / room / night', 'awebooking' ), esc_html( awebooking( 'currency' )->get_symbol() ) ),
					'validate'        => 'required|price',
					'sanitization_cb' => 'awebooking_sanitize_price',
				],
				[
					'id'              => '_rate_label',
					'type'            => 'text_medium',
					'name'            => esc_html__( 'Public name (optional)', 'awebooking' ),
					'desc'            => esc_html__( 'E.g. Best rate available (BAR), Room only, etc...', 'awebooking' ),
					'sanitization_cb' => 'sanitize_text_field',
				],
			],
		]);

		$pricing->add_row( [
			'id'             => '_row_length_of_stay_',
			'flex_columns'   => 4,
			'fields'         => [
				[
					'id'         => 'minimum_night',
					'type'       => 'text_small',
					'name'       => esc_html__( 'Min length of stay', 'awebooking' ),
					'append'     => esc_html__( 'night(s)', 'awebooking' ),
					'default'    => 1,
					'validate'   => 'required|numeric|min:1',
					'sanitization_cb' => 'absint',
				],
				[
					'id'         => '_maximum_los',
					'type'       => 'text_small',
					'name'       => esc_html__( 'Max length of stay', 'awebooking' ),
					'append'     => esc_html__( 'night(s)', 'awebooking' ),
					'default'    => 365,
					'validate'   => 'required|numeric|min:0',
					'sanitization_cb' => 'absint',
				],
			],
		]);

		/*$pricing->add_field( [
			'id'              => '_extra_guest_charge',
			'type'            => 'toggle',
			'desc'            => esc_html__( 'Charge for additional guest?', 'awebooking' ),
			'default'         => true,
		]);

		$pricing->add_field( [
			'id'   => 'per_person_pricing',
			'type' => 'per_person_pricing',
			'deps' => [ '_extra_guest_charge', '==', true ],
		]);*/
	}

	/**
	 * Register the services fields.
	 *
	 * @return void
	 */
	protected function register_services_fields() {
		$services = $this->metabox->add_section( 'services', [
			'title' => esc_html__( 'Services', 'awebooking' ),
		]);

		$services->add_field( [
			'id'              => '_services',
			'type'            => '_none_',
			'name'            => esc_html__( 'Services', 'awebooking' ),
			'save_field'      => false,
			'render_field_cb' => $this->categories_box_callback( Constants::HOTEL_SERVICE ),
		]);
	}

	/**
	 * Register the amenities fields.
	 *
	 * @return void
	 */
	protected function register_amenities_fields() {
		$amenities = $this->metabox->add_section( 'amenities', [
			'title' => esc_html__( 'Amenities', 'awebooking' ),
		]);

		$amenities->add_field( [
			'id'              => '_amenities',
			'type'            => '_none_',
			'name'            => esc_html__( 'Amenities', 'awebooking' ),
			'save_field'      => false,
			'render_field_cb' => $this->categories_box_callback( Constants::HOTEL_AMENITY ),
		]);
	}

	/**
	 * Register the description fields.
	 *
	 * @return void
	 */
	protected function register_description_fields() {
		$description = $this->metabox->add_section( 'description', [
			'title' => esc_html__( 'Description', 'awebooking' ),
		]);

		$description->add_field([
			'id'         => 'gallery',
			'type'       => 'file_list',
			'name'       => esc_html__( 'Gallery', 'awebooking' ),
			'query_args' => [ 'type' => 'image' ],
			'text'       => [ 'add_upload_files_text' => esc_html__( 'Set gallery', 'awebooking' ) ],
		]);

		// Please do not change "excerpt" ID.
		$description->add_field( [
			'id'          => 'excerpt',
			'type'        => 'wysiwyg',
			'name'        => esc_html__( 'Short Description', 'awebooking' ),
			'save_field'  => false,
			'escape_cb'   => false,
			'options'     => [ 'textarea_rows' => 7 ],
			'default_cb'  => function() {
				return get_post_field( 'post_excerpt', get_the_ID() );
			},
		]);
	}

	/**
	 * Generate `post_categories_meta_box` callback.
	 *
	 * @param  string $taxonomy The taxonomy.
	 * @return Clusure
	 */
	protected function categories_box_callback( $taxonomy ) {
		return function() use ( $taxonomy ) {
			post_categories_meta_box( get_post(), [ 'args' => [ 'taxonomy' => $taxonomy ] ] );
			// Temp commit.
			?>
			<style>
				#hotel_extra_service-add-toggle {
					display: none;
				}
			</style>
			<?php
		};
	}

	/**
	 * Generate datalist HTML callback.
	 *
	 * @param  int $min Min.
	 * @param  int $max Max.
	 * @return Closure
	 */
	protected function datalist_number_callback( $min, $max ) {
		return function( $field_args, $field ) use ( $min, $max ) {
			echo '<datalist id="' . esc_attr( $field->id() ) . '_datalist">';

			for ( $i = $min; $i <= $max; $i++ ) {
				echo '<option value="' . esc_attr( $i ) . '">';
			}

			echo '</datalist>';
		};
	}
}
