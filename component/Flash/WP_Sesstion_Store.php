<?php
namespace AweBooking\Component\Flash;

use Awethemes\WP_Session\WP_Session;

class WP_Sesstion_Store implements Session_Store {
	/**
	 * The session instance.
	 *
	 * @var \Awethemes\WP_Session\WP_Session
	 */
	protected $session;

	/**
	 * Create a new session store instance.
	 *
	 * @param \Awethemes\WP_Session\WP_Session $session The WP_Session class instance.
	 */
	public function __construct( WP_Session $session ) {
		$this->session = $session;
	}

	/**
	 * {@inheritdoc}
	 */
	public function flash( $name, $data ) {
		$this->session->flash( $name, $data );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_flash( $name, $default = null ) {
		return $this->session->pull( $name, $default );
	}
}
