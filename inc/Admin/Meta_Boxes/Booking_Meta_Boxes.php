<?php
namespace AweBooking\Admin\Meta_Boxes;

use AweBooking\Room;
use AweBooking\Booking;
use AweBooking\Room_Type;
use AweBooking\Service;
use AweBooking\AweBooking;
use AweBooking\Pricing\Price;
use AweBooking\Currency\Currency;
use AweBooking\Support\Date_Period;
use AweBooking\Support\Formatting;
use AweBooking\Support\Utils;
use Skeleton\CMB2\CMB2;
use AweBooking\Support\Date_Utils;

class Booking_Meta_Boxes extends Meta_Boxes_Abstract {
	/**
	 * Post type ID to register meta-boxes.
	 *
	 * @var string
	 */
	protected $post_type = 'awebooking';

	/**
	 * Constructor of class.
	 */
	public function __construct() {
		parent::__construct();

		// Register metaboxes.
		$this->register_main_metabox();
		$this->register_customer_metabox();

		// Register/un-register metaboxes.
		add_action( 'edit_form_after_title', array( $this, 'booking_title' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'handler_meta_boxes' ), 10 );
	}

	/**
	 * Remove some un-used meta boxes.
	 */
	public function handler_meta_boxes() {
		remove_meta_box( 'slugdiv', $this->post_type, 'normal' );
		remove_meta_box( 'submitdiv', $this->post_type, 'side' );
		remove_meta_box( 'commentstatusdiv', $this->post_type, 'normal' );

		add_meta_box( 'awebooking_booking_action', esc_html__( 'Booking Action', 'awebooking' ), [ $this, 'output_action_metabox' ], AweBooking::BOOKING, 'side', 'high' );

		// add_meta_box( 'awebooking_booking_infomations', esc_html__( 'Booking Infomation', 'awebooking' ), [ $this, 'output_booking_infomation' ], AweBooking::BOOKING );
	}

	public function output_booking_infomation() {
	}

	/**
	 * Prints the booking title.
	 *
	 * @access private
	 *
	 * @param  WP_Post $post WP_Post instance.
	 * @return void
	 */
	public function booking_title( $post ) {
		if ( AweBooking::BOOKING !== $post->post_type ) {
			return;
		}

		$the_booking = new Booking( $post );

		printf( '<h1 class="wp-heading-inline awebooking-title">%s <span>#%s</span></h1>', esc_html__( 'Booking', 'awebooking' ), $post->ID );

		if ( $the_booking['transaction_id'] ) {
			echo '<br>';

			if ( $the_booking['payment_method_title'] ) {
				echo '<span class="">' . esc_html__( 'Via', 'awebooking' ) . ' ' . esc_html( $the_booking->get_payment_method_title() ) . '</span>';
			}

			echo ' | ';

			echo '<span class="">' . esc_html__( 'Transaction ID:', 'awebooking' ) . ' ' . esc_html( $the_booking->get_transaction_id() ) . '</span>';
		}
	}

	/**
	 * Output the action metabox.
	 *
	 * @param WP_Post $post WP_Post object instance.
	 */
	public function output_action_metabox( $post ) {
		include __DIR__ . '/views/booking-action.php';
	}

	/**
	 * Output the action metabox.
	 *
	 * @param WP_Post $post WP_Post object instance.
	 */
	public function output_rooms_metabox( $post ) {
		global $the_booking;

		wp_enqueue_script( 'awebooking-create-booking' );

		$booking_rooms = [];
		if ( isset( $_GET['action'] ) && isset( $_GET['post'] ) ) {
			$the_booking = new Booking( $post );

			if ( $the_booking['room_id'] ) {
				$booking_rooms['room'] = (new Room( $the_booking['room_id'] ))->to_array();
				$booking_rooms['room_type'] = (new Room_Type( $booking_rooms['room']['room_type'] ))->to_array();
				$booking_rooms['price'] = '';

				$booking_rooms['check_in'] = $the_booking['check_in'];
				$booking_rooms['check_out'] = $the_booking['check_out'];
				$booking_rooms['nights'] = $the_booking->get_nights();

				if ( $the_booking['total'] ) {
					$booking_rooms['price'] = (string) $the_booking['total'];
				}
			}
		}

		?>

		<script type="text/javascript">
			var BOOKING_ROOMS = <?php echo json_encode( $booking_rooms ); ?>;
		</script>

		<div id="booking-rooms">
			<div class="book-overlay" v-show="doingAjax"><span class="spinner"></span></div>

			<div class="booking-rooms-left">
				<button class="button" v-on:click.prevent="ajaxRequest()">Check availability</button>
			</div>

			<div class="booking-rooms-right">
				<div v-for="(availability, index) in availability_result" class="room-single">
					<h4 style="margin: 0;">{{ availability.room_type.location_name }}: {{ availability.room_type.title }} - {{ availability.room_type.base_price }}/night</h4>
					<p>Available {{ availability.available_rooms }} room(s) with price: {{ availability.price }}.</p>
					<button class="button" v-on:click.prevent="addRoom(index)">+ Set room</button>
				</div>
			</div>

			<div class="clear" style="margin-bottom: 25px;"></div>

			<div class="booking-rooms-table" v-if="booked_rooms.room" style="background-color: #f8f8f8;">
				<input type="hidden" name="booking_room_id" :value="booked_rooms.room.id">
				<input type="hidden" name="booking_room_type_id" :value="booked_rooms.room_type.id">
				<input type="hidden" name="booking_room_total" :value="booked_rooms.price">

				<strong>{{ booked_rooms.room_type.location_name }}: {{ booked_rooms.room_type.title }}</strong> ( {{  booked_rooms.room.name }} )

				<br> for {{ booked_rooms.nights }} night(s) from <strong> {{ booked_rooms.check_in }} </strong> to <strong>{{ booked_rooms.check_out }}</strong>

				<br>Price: <strong>{{ booked_rooms.price }}</strong>
			</div>

			<hr>

			<?php if ( $the_booking['request_services']): ?>
				<?php foreach ( $the_booking['request_services'] as $key => $value ) : $_service = new Service( $key ); ?>
					<?php if ( ! $_service->exists()) continue; ?>

					<p><?php echo $_service->get_name(); ?></p>
				<?php endforeach ?>
			<?php endif ?>
		</div>

		<style type="text/css">
			#booking-rooms {
				overflow: hidden;
				position: relative;
			}
			.room-single {
				border-bottom: solid 1px #eee; margin-bottom: 15px; padding-bottom: 10px;
			}
			.book-overlay {
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				position: absolute;
				background-color: rgba(255,255,255,0.75);
			}
			.book-overlay .spinner {
				float: none;
				margin: auto;
				display: block;
				margin-top: 50px;
				visibility: visible;
			}
		</style>
		<?php
	}

	/**
	 * Add meta boxes to this post type.
	 *
	 * @access private
	 *
	 * @return void
	 */
	public function register_main_metabox() {
		$metabox = $this->create_metabox( 'awebooking_booking', [
			'title'      => esc_html__( 'Booking Infomation', 'awebooking' ),
			'_show_on_cb' => function( $cmb ) {
				$booking = new Booking( absint( $cmb->object_id() ) );

				return $booking->is_editable();
			},
		]);

		$metabox->add_field( array(
			'id'                => 'booking_check_in',
			'type'              => 'text_date',
			'name'              => esc_html__( 'Check-in / Check-out', 'awebooking' ),
			'validate_label'    => esc_html__( 'Check in', 'awebooking' ),
			'validate'          => 'required|date',
			'attributes'        => [ 'placeholder' => AweBooking::DATE_FORMAT ],
			'date_format'       => AweBooking::DATE_FORMAT,
			'render_field_cb'   => function( $field_args, $field ) {
				$secondary = $field->get_cmb()->get_field( 'booking_check_out' );

				skeleton_render_field( $field );
				printf( ' <span>%s</span> ', esc_html__( 'to', 'awebooking' ) );
				skeleton_render_field( $secondary );

				skeleton_display_field_errors( $secondary );
			},
		));

		$metabox->add_field( array(
			'id'          => 'booking_check_out',
			'type'        => 'text_date',
			'name'        => esc_html__( 'Check-out', 'awebooking' ),
			'validate'    => 'required|date',
			'validate_cb' => [ $this, 'validate_date_period' ],
			'attributes'  => [ 'placeholder' => AweBooking::DATE_FORMAT ],
			'date_format' => AweBooking::DATE_FORMAT,
			'show_on_cb'  => '__return_false',
		));

		$metabox->add_field( array(
			'id'               => 'booking_adults',
			'type'             => 'text_small',
			'name'             => esc_html__( 'Booking capacity:', 'awebooking' ),
			'default'          => 1,
			'validate'         => 'required|numeric|min:1',
			'validate_label'   => esc_html__( 'Adults', 'awebooking' ),
			'sanitization_cb'  => 'absint',
			'render_field_cb'  => function( $field_args, $field ) {
				$secondary = $field->get_cmb()->get_field( 'booking_children' );

				echo '<div class="skeleton-input-group">';
				skeleton_render_field( $field );
				echo '<span class="skeleton-input-group__addon">' . esc_html__( 'adults', 'awebooking' ) . '</span></div>';

				echo '<div class="skeleton-input-group">';
				skeleton_render_field( $secondary );
				echo '<span class="skeleton-input-group__addon">' . esc_html__( 'children', 'awebooking' ) . '</span></div>';

				skeleton_display_field_errors( $secondary );
			},
		));

		$metabox->add_field( array(
			'id'              => 'booking_children',
			'type'            => 'text_small',
			'name'            => esc_html__( 'Children', 'awebooking' ),
			'default'         => 0,
			'validate'        => 'required|numeric|min:0',
			'sanitization_cb' => 'absint',
			'show_on_cb'      => '__return_false', // NOTE: We'll handler display in "booking_adults".
		));

		$metabox->add_field( array(
			'id'              => 'booking_room_id',
			'type'            => 'callback',
			'name'            => esc_html__( 'Booking room', 'awebooking' ),
			'render_field_cb' => function() {
				global $post;
				$this->output_rooms_metabox( $post );
			},
		));

		$metabox->add_field( array(
			'id'              => 'total_price',
			'type'            => 'text_small',
			'name'            => esc_html__( 'Total price', 'awebooking' ),
			'default'         => 0,
			'validate'        => 'required|numeric:min:0',
			'sanitization_cb' => 'abkng_sanitize_price',
			'before'          => '<div class="skeleton-input-group">',
			'after'           => '<span class="skeleton-input-group__addon">' . awebooking( 'currency' )->get_symbol() . '</span></div>',
		));
	}

	/**
	 * Add customer meta box to this post type.
	 *
	 * @access private
	 *
	 * @return void
	 */
	public function register_customer_metabox() {
		$metabox = $this->create_metabox( 'awebooking_booking_general', [
			'title' => esc_html__( 'General Details', 'awebooking' ),
		]);

		// Temp data.
		$metabox->add_field( array(
			'id'                => '_booking_datetime',
			'type'              => 'text_datetime_timestamp',
			'name'              => esc_html__( 'Booking Date', 'awebooking' ),
			'save_field'        => false, // Dont save this field as metadata.
			'date_format'       => 'Y-m-d',
			'time_format'       => 'H:i:s',
			'default_cb'        => function( $a ) {
				global $post;
				return Date_Utils::create_datetime( $post->post_date )->getTimestamp();
			},
			'attributes'        => [
				'data-timepicker' => json_encode([
					'timeFormat' => 'HH:mm:ss',
					'stepMinute' => 1,
				]),
			],
		));

		$metabox->add_field( array(
			'id'                => 'customer_id',
			'type'              => 'select',
			'name'              => esc_html__( 'Customer', 'awebooking' ),
			'options'           => wp_data( 'users' ),
			'show_option_none'  => esc_html__( 'Guest', 'awebooking' ),
		));

		// This field is special, we set name as "post_status" standard of
		// WordPress post status, so we'll leave to WP care about that.
		$metabox->add_field( array(
			'id'                => 'post_status',
			'type'              => 'select',
			'name'              => esc_html__( 'Booking status', 'awebooking' ),
			'save_field'        => false, // Dont save this field as metadata.
			'default_cb'        => function() {
				global $post;
				return get_post_status( $post );
			},
			'options'           => Utils::get_booking_statuses(),
		));

		// Customer infomation.
		$metabox->add_field( array(
			'id'                => 'customer_id',
			'type'              => 'select',
			'name'              => esc_html__( 'Customer', 'awebooking' ),
			'options'           => wp_data( 'users' ),
			'show_option_none'  => esc_html__( 'Guest', 'awebooking' ),
		));

		$metabox->add_field( array(
			'id'      => '__booking_customber__',
			'type'    => 'title',
			'name'    => esc_html__( 'Customer Information', 'awebooking' ),
		));

		$metabox->add_field( array(
			'id'      => 'customer_title',
			'type'    => 'select',
			'name'    => esc_html__( 'Title', 'awebooking' ),
			'options' => Utils::get_common_titles(),
		));

		$metabox->add_field( array(
			'id'   => 'customer_first_name',
			'type' => 'text',
			'name' => esc_html__( 'First name', 'awebooking' ),
			'validate' => 'required',
		));

		$metabox->add_field( array(
			'id'   => 'customer_last_name',
			'type' => 'text',
			'name' => esc_html__( 'Last name', 'awebooking' ),
			'validate' => 'required',
		));

		$metabox->add_field( array(
			'id'   => 'customer_phone',
			'type' => 'text',
			'name' => esc_html__( 'Phone number', 'awebooking' ),
			'validate' => 'required',
		));

		$metabox->add_field( array(
			'id'   => 'customer_email',
			'type' => 'text',
			'name' => esc_html__( 'Email address', 'awebooking' ),
			'validate' => 'email',
		));

		$metabox->add_field( array(
			'id'   => 'customer_address',
			'type' => 'text',
			'name' => esc_html__( 'Address', 'awebooking' ),
		));

		$metabox->add_field( array(
			'id'   => 'customer_company',
			'type' => 'text',
			'name' => esc_html__( 'Company', 'awebooking' ),
		));

		$metabox->add_field( array(
			'id'   => 'customer_note',
			'type' => 'textarea',
			'name' => esc_html__( 'Customer Notes', 'awebooking' ),
			'sanitization_cb' => 'sanitize_textarea_field',
		));
	}

	/**
	 * //
	 *
	 * @access private
	 *
	 * @param  WP_Error $validity  //.
	 * @param  string   $check_out //.
	 * @param  CMB2     $cmb2      //.
	 * @return void
	 */
	public function validate_date_period( $validity, $check_out, CMB2 $cmb2 ) {
		$check_in = isset( $cmb2->data_to_save['booking_check_in'] ) ? $cmb2->data_to_save['booking_check_in'] : null;

		try {
			new Date_Period( $check_in, $check_out, false );
		} catch ( \Exception $e ) {
			$validity->add( 'date_period', $e->getMessage() );
		}
	}
}
