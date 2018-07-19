<?php

namespace AweBooking\Admin;

use AweBooking\Plugin;
use AweBooking\Support\Fluent;

class Fluent_Settings extends Fluent {
	/**
	 * The plugin instance.
	 *
	 * @var \AweBooking\Plugin
	 */
	protected $plugin;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Plugin $plugin The plugin instance.
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get( $key, $default = null ) {
		return $this->plugin->get_option( $key, $default );
	}
}
