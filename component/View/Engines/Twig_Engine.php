<?php

namespace AweBooking\Component\View\Engines;

use Twig_Environment;

class Twig_Engine extends Php_Engine {
	/**
	 * The Twig Environment instance.
	 *
	 * @var \Twig_Environment
	 */
	protected $environment;

	/**
	 * Constructor.
	 *
	 * @param \Twig_Environment $environment
	 */
	public function __construct( Twig_Environment $environment ) {
		$this->environment = $environment;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get( $name, array $data = [] ) {
		return $this->environment->render( $name, $data );
	}
}
