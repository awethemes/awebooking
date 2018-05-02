<?php
namespace AweBooking\Email\Templates;

use AweBooking\Email\Mailable;

class Reserved_Booking extends Mailable {
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
		$this->id             = 'reserved_booking';
		$this->title          = esc_html__( 'Reserved Booking', 'awebooking' );
		$this->description    = esc_html__( 'Sent when a booking is reserved.', 'awebooking' );
		$this->customer_email = true;
		$this->placeholders   = [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function init() {
		// TODO: change hook
		// add_action( 'awebooking/awebooking/status_changed', [ $this, 'trigger' ], 10, 3 );
	}

	/**
	 * Trigger send email.
	 *
	 * @return void
	 */
	public function trigger( $new_status, $old_status, $booking ) {
		$this->build( $booking )->send();
	}

	/**
	 * Prepare data for sending.
	 *
	 * @param  \AweBooking\Model\Booking $booking       The booking instance.
	 * @return void
	 */
	protected function prepare_data( $booking ) {
		$this->booking = $booking;
		$this->recipient = $booking->get( 'customer_email' );

		// $this->placeholders = $this->set_replacements( $booking );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_subject() {
		return esc_html__( "Your {site_title} booking receipt from {created_date}", "awebooking" );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_content() {
		ob_start();
		?>
		<p><?php echo esc_html__( "Your booking is reserved until we confirm payment has been received. Your booking details are shown below for your reference:", 'awebooking' ); ?></p>
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
		return abrs_get_template_content( 'emails/reserved-booking.php', [
			'email'         => $this,
			'booking'       => $this->booking,
			'content'       => $this->format_string( $this->get_option( 'content' ) ),
		]);
	}
}
