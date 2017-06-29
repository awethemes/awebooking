<?php
namespace AweBooking;

use Skeleton\Taxonomy;

class Service_Tax extends Taxonomy {
	/**
	 * Make a `room_type` post-type.
	 */
	public function __construct() {
		parent::__construct(
			AweBooking::HOTEL_SERVICE,
			'room_type',
			esc_html__( 'Extra Service', 'awebooking' ),
			esc_html__( 'Extra Services', 'awebooking' )
		);

		$this->set([
			'public'             => false,
			'hierarchical'       => false,
			'show_admin_column'  => false,
			'show_in_quick_edit' => false,
		]);

		add_action( 'admin_menu', array( $this, 'remove_meta_box' ) );
		add_action( 'save_post', array( $this, 'update_terms' ), 1000000 );
		add_action( 'wp_ajax_set_term_meta', array( $this, 'set_term_meta' ) );
	}

	/**
	 * Register metabox.
	 */
	public function meta_boxes() {
		$this->add_field([
			'name'      => esc_html__( 'Operation', 'awebooking' ),
			'id'        => 'operation',
			'type'      => 'select',
			'options'   => static::operation_options(),
			'sanitization_cb' => array( $this, 'sanitize_operation' ),
			'render_field_cb'   => array( $this, '_room_field_callback' ),
		]);

		$this->add_field([
			'name' => esc_html__( 'Price', 'awebooking' ),
			'id'   => 'price',
			'type' => 'text_small',
			'validate'   => 'required|numeric:min:0',
			'sanitization_cb' => 'abkng_sanitize_price',
			'show_on_cb' => '__return_false',
		]);

		$this->add_field([
			'name'      => esc_html__( 'Type', 'awebooking' ),
			'id'        => 'type',
			'type'      => 'select',
			'options'   => static::type_options(),
			'sanitization_cb' => array( $this, 'sanitize_type' ),
		]);
	}

	/**
	 * Render rooms callback.
	 *
	 * @return void
	 */
	public function _room_field_callback( $field_args, $field ) {
		$cmb2 = $field->get_cmb();
		$price = $cmb2->get_field( 'price' );

		echo '<div class="skeleton-input-group">';
		skeleton_render_field( $field );
		echo '</div>';

		echo '<div class="skeleton-input-group">';
		skeleton_render_field( $price );

		echo '<span class="skeleton-input-group__addon"><span class="awebooking-price-field">' . awebooking( 'currency' )->get_symbol() . '</span><span class="awebooking-value-field" style="display:none;">' . esc_html__( '%', 'awebooking' ) . '</span></span>';
		echo '</div>';
		?>
		<script>
			jQuery(function($) {
				var displayCallback = function() {
					var operation = $('#operation').val();
					if (operation === 'increase' || operation === 'decrease') {
						$('.awebooking-price-field').hide();
						$('.awebooking-value-field').show();
					} else {
						$('.awebooking-value-field').hide();
						$('.awebooking-price-field').show();
					}
				}

				displayCallback();
				$('#operation').on('change', displayCallback);
			});
		</script>
		<?php
		skeleton_display_field_errors( $price );
	}

	/**
	 * Operation options supported.
	 *
	 * @return array
	 */
	public static function operation_options() {
		return [
			'add'        => esc_html__( 'Add to price', 'awebooking' ),
			'add-daily'  => esc_html__( 'Add to price per night', 'awebooking' ),

			'add-person'        => esc_html__( 'Add to price per person', 'awebooking' ),
			'add-person-daily'  => esc_html__( 'Add to price per person per night', 'awebooking' ),

			'sub'        => esc_html__( 'Subtract from price', 'awebooking' ),
			'sub-daily'  => esc_html__( 'Subtract from price per night', 'awebooking' ),
			'increase'   => esc_html__( 'Increase price by % amount', 'awebooking' ),
			'decrease'   => esc_html__( 'Decrease price by % amount', 'awebooking' ),
		];
	}

	/**
	 * Type options supported.
	 *
	 * @return array
	 */
	public static function type_options() {
		return apply_filters( 'awebooking/type_options_supported', [
			'optional'  => esc_html__( 'Optional', 'awebooking' ),
			'mandatory' => esc_html__( 'Mandatory', 'awebooking' ),
		] );
	}

	/**
	 * Sanitize operation.
	 *
	 * @param  string $value value.
	 * @return string
	 */
	public function sanitize_operation( $value ) {
		$operation_options = static::operation_options();
		$valid_values = [];
		foreach ( $operation_options as $key => $operation_option ) {
			$valid_values[] = $key;
		}

		if ( ! in_array( $value, $valid_values ) ) {
			return 'add';
		}

		return $value;
	}

	/**
	 * Sanitize type.
	 *
	 * @param  string $value value.
	 * @return string
	 */
	public function sanitize_type( $value ) {
		$type_options = static::type_options();
		$valid_values = [];
		foreach ( $type_options as $key => $operation_option ) {
			$valid_values[] = $key;
		}

		if ( ! in_array( $value, $valid_values ) ) {
			return 'optional';
		}

		return $value;
	}

	/**
	 * Remove metabox.
	 */
	public function remove_meta_box() {
		remove_meta_box( 'tagsdiv-hotel_extra_service', 'room_type', 'side' );
	}

	/**
	 * Update terms.
	 *
	 * @param  int $post_id post_id.
	 * @return void
	 */
	public function update_terms( $post_id ) {
		// If this is just a revision, don't send the email.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// If this isn't a 'room_type' post, don't update it.
		if ( get_post_type( $post_id ) !== AweBooking::ROOM_TYPE ) {
			return;
		}

		if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
			return;
		}

		// Alway check admin referer.
		check_admin_referer( 'awebooking-sync-services', '_awebooking_nonce' );

		$services = [];
		if ( isset( $_POST['awebooking_services'] ) && is_array( $_POST['awebooking_services'] ) ) {
			$services = array_unique(
				array_map( 'intval', $_POST['awebooking_services'] )
			);
		}

		$term_taxonomy_ids = wp_set_object_terms(
			$post_id, $services, AweBooking::HOTEL_SERVICE, false
		);
	}

	/**
	 * Set new term meta.
	 */
	public function set_term_meta() {
		$service_name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$service_price = isset( $_POST['price'] ) ? sanitize_text_field( wp_unslash( $_POST['price'] ) ) : '';
		$service_operation = isset( $_POST['operation'] ) ? sanitize_text_field( wp_unslash( $_POST['operation'] ) ) : '';
		$service_type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
		$post_id = isset( $_POST['post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) : '';

		$set_post_term = wp_set_post_terms( $post_id, $service_name, AweBooking::HOTEL_SERVICE, true );

		if ( ! is_wp_error( $set_post_term ) ) {
			// Get term_id, set default as 0 if not set
			$term_id = isset( $set_post_term[0] ) ? $set_post_term[0] : 0;

			update_term_meta( $term_id, 'price', $service_price, false );
			update_term_meta( $term_id, 'operation', $service_operation, false );
			update_term_meta( $term_id, 'type', $service_type, false );

		} else {
			return;
		}

		wp_send_json_success($term_id);
	}
}
