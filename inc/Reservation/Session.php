<?php
namespace AweBooking\Reservation;

use AweBooking\Model\Common\Timespan;
use Awethemes\WP_Session\Session as WP_Session;

class Session {
	/* constants */
	const DEFAULT_GROUP = '_reservation_default';
	const ADMIN_GROUP = '_reservation_admin';

	/**
	 * The WP_Session instance.
	 *
	 * @var \Awethemes\WP_Session\WP_Session
	 */
	protected $wp_session;

	/**
	 * The session key name.
	 *
	 * @var string
	 */
	protected $session_name;

	/**
	 * The session lifetime in minutes.
	 *
	 * @var int
	 */
	protected $lifetime;

	/**
	 * Keep the session alive.
	 *
	 * @var boolean
	 */
	protected $keep_session = false;

	/**
	 * Constructor.
	 *
	 * @param WP_Session $wp_session   The WP_Session instance.
	 * @param string     $session_name The session key name.
	 * @param integer    $lifetime     The session lifetime in minutes.
	 */
	public function __construct( WP_Session $wp_session, $session_name = self::DEFAULT_GROUP, $lifetime = 30 ) {
		$this->wp_session   = $wp_session;
		$this->session_name = $session_name;
		$this->lifetime     = $lifetime;
	}

	/**
	 * Init the hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wp_loaded', [ $this, 'get_room_stays' ] );
	}

	public function get_room_stays() {
		$room_stay = $this->wp_session->get( 'room_stays' );

//		dump( $room_stay );
	}


	/**
	 * Resolve the reservation from session.
	 *
	 * @param  string $session_id Optional, the reservation session ID to compare.
	 * @return Reservation|null
	 */
	public function resolve( $session_id = null ) {
		$payload = $this->wp_session->get( $this->session_name );

		if ( empty( $payload['data'] )
			|| empty( $payload['timeout'] )
			|| empty( $payload['session_id'] ) ) {
			return;
		}

		// Verify payload timeout.
		if ( ! $this->keep_session && time() > $payload['timeout'] ) {
			$this->flush();
			return;
		}

		if ( ! is_null( $session_id ) && $session_id !== $payload['session_id'] ) {
			return;
		}

		$reservation = $payload['data'];
		$reservation->set_session_id( $payload['session_id'] );

		return $reservation;
	}

	/**
	 * Update the reservation into the session.
	 *
	 * @param  \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 * @return bool
	 */
	public function update( Reservation $reservation ) {
		if ( ! $stored_reservation = $this->resolve() ) {
			return false;
		}

		$this->wp_session->put( $this->session_name . '.data',
			$reservation
		);

		return true;
	}

	/**
	 * Store the reservation into the session.
	 *
	 * @param  \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 * @return void
	 */
	public function store( Reservation $reservation ) {
		$this->wp_session->put( $this->session_name, [
			'data'       => $reservation,
			'timeout'    => time() + ( $this->lifetime * MINUTE_IN_SECONDS ),
			'session_id' => $this->generate_session_id( $reservation ),
		]);
	}

	/**
	 * Keep the session.
	 *
	 * @param  bool $keep Keep or not.
	 * @return void
	 */
	public function keep( $keep = true ) {
		$this->keep_session = $keep;
	}

	/**
	 * Flush the reservation payload.
	 *
	 * @return void
	 */
	public function flush() {
		$this->wp_session->remove( $this->session_name );
		$this->wp_session->remove( 'cart' );
		$this->wp_session->remove( 'cart_totals' );
		$this->wp_session->remove( 'order_awaiting_payment' );
	}

	/**
	 * Generate the reservation session_id.
	 *
	 * @param  \AweBooking\Reservation\Reservation $reservation The reservation instance.
	 * @return string
	 */
	protected function generate_session_id( Reservation $reservation ) {
		return sha1( $reservation->get_source()->get_uid() . time() );
	}
}
