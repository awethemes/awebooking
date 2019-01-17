<?php

namespace AweBooking\Reservation\Storage;

use WPLibs\Session\Session;

class Session_Store implements Store {
	/**
	 * The WP session instance.
	 *
	 * @var \WPLibs\Session\Session
	 */
	protected $session;

	/**
	 * Constructor.
	 *
	 * @param \WPLibs\Session\Session $session The session store instance.
	 */
	public function __construct( Session $session ) {
		$this->session = $session;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get( $id ) {
		return $this->session->get( $id );
	}

	/**
	 * {@inheritdoc}
	 */
	public function put( $id, $data ) {
		$this->session->put( $id, $data );
	}

	/**
	 * {@inheritdoc}
	 */
	public function flush( $id ) {
		$this->session->remove( $id );
	}
}
