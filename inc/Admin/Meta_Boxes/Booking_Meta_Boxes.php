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
use Skeleton\CMB2\CMB2;
use AweBooking\Support\Date_Utils;
use AweBooking\Support\Mailer;
use AweBooking\Notification\Booking_Cancelled;
use AweBooking\Notification\Booking_Processing;
use AweBooking\Notification\Booking_Completed;

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
		add_action( 'awebooking/save_booking', [ $this, 'handler_booking_actions' ], 10, 1 );
		add_action( 'admin_init', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_ajax_nopriv_awebooking/delete_booking_note', array( $this, 'delete_booking_note' ) );
		add_action( 'wp_ajax_awebooking/delete_booking_note', array( $this, 'delete_booking_note' ) );
		add_action( 'wp_ajax_nopriv_awebooking/add_booking_note', array( $this, 'add_booking_note' ) );
		add_action( 'wp_ajax_awebooking/add_booking_note', array( $this, 'add_booking_note' ) );
	}

	/**
	 * Remove some un-used meta boxes.
	 */
	public function handler_meta_boxes() {
		remove_meta_box( 'slugdiv', $this->post_type, 'normal' );
		remove_meta_box( 'submitdiv', $this->post_type, 'side' );
		remove_meta_box( 'commentstatusdiv', $this->post_type, 'normal' );

		add_meta_box( 'awebooking_booking_action', esc_html__( 'Booking Action', 'awebooking' ), [ $this, 'output_action_metabox' ], AweBooking::BOOKING, 'side', 'high' );
		add_meta_box( 'awebooking-booking-notes', esc_html__( 'Booking notes', 'awebooking' ), [ $this, 'booking_note_output' ], AweBooking::BOOKING, 'side', 'default' );

		// add_meta_box( 'awebooking_booking_infomations', esc_html__( 'Booking Infomation', 'awebooking' ), [ $this, 'output_booking_infomation' ], AweBooking::BOOKING );
	}

	public function output_booking_infomation() {
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'awebooking-booking', AweBooking()->plugin_url() . '/assets/js/admin/booking.js', array( 'jquery' ), AweBooking::VERSION, true );
		wp_localize_script( 'awebooking-booking', 'awebooking_booking_ajax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		));
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
			'sanitization_cb' => 'awebooking_sanitize_price_number',
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
			'options'           => awebooking()->get_booking_statuses(),
		));

		// Customer infomation.
		$metabox->add_field( array(
			'id'                => 'customer_id',
			'type'              => 'select',
			'name'              => esc_html__( 'Customer', 'awebooking' ),
			'options_cb'        => wp_data_callback( 'users' ),
			'show_option_none'  => esc_html__( 'Guest', 'awebooking' ),
		));

		$metabox->add_field( array(
			'id'      => '__booking_customber__',
			'type'    => 'title',
			'name'    => esc_html__( 'Customer Information', 'awebooking' ),
		));

		$metabox->add_field( array(
			'id'               => 'customer_title',
			'type'             => 'select',
			'name'             => esc_html__( 'Title', 'awebooking' ),
			'options'          => awebooking_get_common_titles(),
			'show_option_none' => true,
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

	/**
	 * Handler booking actions.
	 */
	public function handler_booking_actions( Booking $booking ) {

		// Handle button actions.
		if ( empty( $_POST['awebooking_action'] ) ) {
			return;
		}

		$action = $_POST['awebooking_action'];

		if ( strstr( $action, 'send_email_' ) ) {

			do_action( 'awebooking/before_resend_order_emails', $booking );

			// Load mailer.
			$email_to_send = str_replace( 'send_email_', '', $action );

			switch ( $email_to_send ) {
				case 'cancelled_order':
					try {
						$mail = Mailer::to( $booking->get_customer_email() )->send( new Booking_Cancelled( $booking ) );
					} catch ( \Exception $e ) {
						// ...
					}

					if ( $mail ) {
						$booking->add_booking_note( __( 'Cancelled email notification manually sent.', 'awebooking' ), false, true );
					}
					break;

				case 'customer_processing_order':
					try {
						$mail = Mailer::to( $booking->get_customer_email() )->send( new Booking_Processing( $booking ) );
					} catch ( \Exception $e ) {
						// ...
					}
					if ( $mail ) {
						$booking->add_booking_note( __( 'Processing email notification manually sent.', 'awebooking' ), false, true );
					}
					break;

				case 'customer_completed_order':
					try {
						$mail = Mailer::to( $booking->get_customer_email() )->send( new Booking_Completed( $booking ) );
					} catch ( \Exception $e ) {
						// ...
					}

					if ( $mail ) {
						$booking->add_booking_note( __( 'Completed email notification manually sent.', 'awebooking' ), false, true );
					}
					break;

				default:
					return;
					break;
			}

			do_action( 'awebooking/after_resend_order_email', $booking, $email_to_send );
		}
	}

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post post.
	 */
	public function booking_note_output( $post ) {
		global $post;

		$args = array(
			'post_id'   => $post->ID,
			'orderby'   => 'comment_ID',
			'order'     => 'DESC',
			'approve'   => 'approve',
			'type'      => 'booking_note',
		);

		remove_filter( 'comments_clauses', array( $this, 'exclude_order_comments' ), 10, 1 );

		$notes = get_comments( $args );

		add_filter( 'comments_clauses', array( $this, 'exclude_order_comments' ), 10, 1 );

		echo '<ul class="booking_notes">';

		if ( $notes ) {

			foreach ( $notes as $note ) {

				$note_classes   = array( 'note' );
				$note_classes[] = get_comment_meta( $note->comment_ID, 'is_customer_note', true ) ? 'customer-note' : '';
				$note_classes[] = ( __( 'AweBooking', 'awebooking' ) === $note->comment_author ) ? 'system-note' : '';
				$note_classes   = apply_filters( 'awebooking/booking_note_class', array_filter( $note_classes ), $note );
				?>
				<li rel="<?php echo absint( $note->comment_ID ); ?>" class="<?php echo esc_attr( implode( ' ', $note_classes ) ); ?>">
					<div class="note_content">
						<?php echo wpautop( wptexturize( wp_kses_post( $note->comment_content ) ) ); ?>
					</div>
					<p class="meta">
						<abbr class="exact-date" title="<?php echo $note->comment_date; ?>"><?php printf( esc_html__( 'added on %1$s at %2$s', 'awebooking' ), date_i18n( awebooking_option( 'date_format' ), strtotime( $note->comment_date ) ), date_i18n( get_option( 'time_format' ), strtotime( $note->comment_date ) ) ); ?></abbr>
						<?php
						if ( __( 'AweBooking', 'awebooking' ) !== $note->comment_author ) :
							/* translators: %s: note author */
							printf( ' ' . esc_html__( 'by %s', 'awebooking' ), $note->comment_author );
						endif;
						?>
						<a href="#" class="delete_note" role="button"><?php esc_html_e( 'Delete note', 'awebooking' ); ?></a>
					</p>
				</li>
				<?php
			}
		} else {
			echo '<li>' . esc_html__( 'There are no notes yet.', 'awebooking' ) . '</li>';
		}

		echo '</ul>';
		?>
		<div class="add_note">
			<p>
				<label for="add_booking_note"><?php esc_html_e( 'Add note', 'awebooking' ); ?></label>
				<textarea type="text" name="order_note" id="add_booking_note" class="input-text" cols="20" rows="5"></textarea>
			</p>

			<p>
			<?php
			/**
				<label for="booking_note_type" class="screen-reader-text"><?php _e( 'Note type', 'awebooking' ); ?></label>
				<select name="booking_note_type" id="booking_note_type">
					<option value=""><?php _e( 'Private note', 'awebooking' ); ?></option>
					<option value="customer"><?php _e( 'Note to customer', 'awebooking' ); ?></option>
				</select>
			**/
			?>
				<button type="button" class="add_note button"><?php esc_html_e( 'Add', 'awebooking' ); ?></button>
			</p>
		</div>
		<?php
	}

	/**
	 * Exclude booking comments from queries and RSS.
	 *
	 * @param  array $clauses clauses.
	 * @return array
	 */
	public function exclude_booking_comments( $clauses ) {
		$clauses['where'] .= ( $clauses['where'] ? ' AND ' : '' ) . " comment_type != 'booking_note' ";
		return $clauses;
	}

	/**
	 * Add booking note via ajax.
	 */
	public function add_booking_note() {
		$booking_id   = absint( $_POST['booking_id'] );
		$note      = wp_kses_post( trim( stripslashes( $_POST['note'] ) ) );
		$note_type = $_POST['note_type'];

		$is_customer_note = ( isset( $note_type ) && ( 'customer' === $note_type ) ) ? 1 : 0;

		if ( $booking_id > 0 ) {
			$booking      = new Booking( $booking_id );
			$comment_id = $booking->add_booking_note( $note, $is_customer_note, true );

			$new_note = '<li rel="' . esc_attr( $comment_id ) . '" class="note ';
			if ( $is_customer_note ) {
				$new_note .= 'customer-note';
			}
			$new_note .= '"><div class="note_content">';
			$new_note .= wpautop( wptexturize( $note ) );
			$new_note .= '</div><p class="meta"><a href="#" class="delete_note">' . __( 'Delete note', 'awebooking' ) . '</a></p>';
			$new_note .= '</li>';

			return wp_send_json_success( [ 'new_note' => $new_note ], 200 );
		}
		wp_die();
	}

	/**
	 * Delete booking note via ajax.
	 *
	 * @param string $booking_id booking ID
	 * @param string $id note ID
	 * @return WP_Error|array error or deleted message
	 */
	/**
	 * This function contains output data.
	 */
	public function delete_booking_note() {
		$note_id = sanitize_text_field( $_POST['note_id'] );
		$booking_id = sanitize_text_field( $_POST['booking_id'] );

		try {
			if ( empty( $note_id ) ) {
				return wp_send_json_error( [ 'message' => __( 'Invalid booking note ID', 'awebooking' ) ], 400 );
			}

			// Ensure note ID is valid.
			$note = get_comment( $note_id );

			if ( is_null( $note ) ) {
				return wp_send_json_error( [ 'message' => __( 'A booking note with the provided ID could not be found', 'awebooking' ) ], 404 );
			}

			// Ensure note ID is associated with given order.
			if ( $note->comment_post_ID != $booking_id ) {
				return wp_send_json_error( [ 'message' => __( 'The booking note ID provided is not associated with the booking', 'awebooking' ) ], 400 );
			}

			// Force delete since trashed booking notes could not be managed through comments list table.
			$result = wp_delete_comment( $note->comment_ID, true );

			if ( ! $result ) {
				return wp_send_json_error( [ 'message' => __( 'This booking note cannot be deleted', 'awebooking' ) ], 500 );
			}

			do_action( 'awebooking/api_delete_booking_note', $note->comment_ID, $note_id, $this );

			return wp_send_json_success( [ 'note_id' => $note_id ], 200 );

		} catch ( \Exception $e ) {
			//
		}
	}
}
