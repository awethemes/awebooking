<?php
namespace AweBooking\Email\Templates;

use AweBooking\Email\Mailable;

class Admin_Cancelled_Booking extends Mailable {
	/**
	 * The booking instance.
	 *
	 * @var \AweBooking\Model\Booking
	 */
	protected $booking;

	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		$this->id             = 'admin_cancelled_booking';
		$this->title          = esc_html__( 'Cancelled booking', 'awebooking' );
		$this->description    = esc_html__( 'Sent when a booking is cancelled.', 'awebooking' );
		$this->customer_email = false;
		$this->placeholders   = [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function init() {
		add_action( 'awebooking/awebooking/status_changed', [ $this, 'trigger' ], 10, 3 );
	}

	/**
	 * Trigger send email.
	 *
	 * @return void
	 */
	public function trigger( $new_status, $old_status, $booking ) {
		if ( 'awebooking-cancelled' === $new_status ) {
			$this->build( $booking )->send();
		}
	}

	/**
	 * Prepare data for sending.
	 *
	 * @param  \AweBooking\Model\Booking $booking       The booking instance.
	 * @return void
	 */
	protected function prepare_data( $booking ) {
		$this->booking = $booking;

		// $this->placeholders = $this->set_replacements( $booking );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_subject() {
		return esc_html__( '[{site_title}] Cancelled booking (#{booking_id})', 'awebooking' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_content() {
		ob_start();
		?>
		<p><?php echo esc_html__( "The booking #{booking_id} from {customer_name} has been cancelled. The booking was as follows:", 'awebooking' ); ?></p>
		<h2><a class="link" href="<?php echo esc_url( get_edit_post_link( "{booking_id}" ) ); ?>"><?php echo esc_html__( "Booking #{booking_id}", 'awebooking' ); ?></a></h2>
		{contents}
		{customer_details}
		<?php
		return ob_get_clean();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_content_plain() {
		return $this->format_string( $this->get_option( 'content' ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_content_html() {
		return abrs_get_template_content( 'emails/admin-cancelled-booking.php', [
			'email'         => $this,
			'booking'       => $this->booking,
			'content'       => $this->format_string( $this->get_option( 'content' ) ),
		]);
	}
}
