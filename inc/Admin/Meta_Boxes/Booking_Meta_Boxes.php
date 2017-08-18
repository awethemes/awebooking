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
		// $this->register_main_metabox();
		$this->register_customer_metabox();

		// Register/un-register metaboxes.
		add_action( 'edit_form_after_title', array( $this, 'booking_title' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'handler_meta_boxes' ), 10 );
		add_action( 'awebooking/save_booking', [ $this, 'handler_booking_actions' ], 10, 1 );
		add_action( 'admin_init', [ $this, 'enqueue_scripts' ] );
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

		include trailingslashit( __DIR__ ) . 'views/html-booking.php';
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
	 * Add customer meta box to this post type.
	 *
	 * @access private
	 *
	 * @return void
	 */
	public function register_customer_metabox() {
		$metabox = $this->create_metabox( 'awebooking_booking_general', [
			'title' => esc_html__( 'Details', 'awebooking' ),
		]);

		$customer = $metabox->add_section( 'customer_infomation', [
			'title' => esc_html__( 'Customer', 'awebooking' ),
		]);

		$customer->add_field( array(
			'id'               => 'customer_title',
			'type'             => 'select',
			'name'             => esc_html__( 'Title', 'awebooking' ),
			'options'          => awebooking_get_common_titles(),
			'show_option_none' => '---',
		));

		$customer->add_field( array(
			'id'   => 'customer_first_name',
			'type' => 'text',
			'name' => esc_html__( 'First name', 'awebooking' ),
			'validate' => 'required',
		));

		$customer->add_field( array(
			'id'   => 'customer_last_name',
			'type' => 'text',
			'name' => esc_html__( 'Last name', 'awebooking' ),
			'validate' => 'required',
		));

		$customer->add_field( array(
			'id'   => 'customer_phone',
			'type' => 'text',
			'name' => esc_html__( 'Phone number', 'awebooking' ),
			'validate' => 'required',
		));

		$customer->add_field( array(
			'id'   => 'customer_email',
			'type' => 'text',
			'name' => esc_html__( 'Email address', 'awebooking' ),
			'validate' => 'email',
		));

		$customer->add_field( array(
			'id'   => 'customer_address',
			'type' => 'text',
			'name' => esc_html__( 'Address', 'awebooking' ),
		));

		$customer->add_field( array(
			'id'   => 'customer_company',
			'type' => 'text',
			'name' => esc_html__( 'Company', 'awebooking' ),
		));

		$customer->add_field( array(
			'id'   => 'customer_note',
			'type' => 'textarea',
			'name' => esc_html__( 'Customer Notes', 'awebooking' ),
			'sanitization_cb' => 'sanitize_textarea_field',
		));
	}

	/**
	 * Handler booking actions.
	 *
	 * @param  AweBooking\Booking $booking booking obj.
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

		remove_filter( 'comments_clauses', array( $this, 'exclude_booking_comments' ), 10, 1 );

		$notes = get_comments( $args );

		add_filter( 'comments_clauses', array( $this, 'exclude_booking_comments' ), 10, 1 );

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
}
