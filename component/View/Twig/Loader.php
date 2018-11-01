<?php

namespace AweBooking\Component\View\Twig;

use Twig_Source;
use Twig_Error_Loader;
use InvalidArgumentException;
use AweBooking\Component\View\Finder;

/**
 * Loader
 *
 * @noinspection PhpDeprecationInspection
 */
class Loader implements \Twig_LoaderInterface, \Twig_ExistsLoaderInterface, \Twig_SourceContextLoaderInterface {
	/**
	 * The view finder.
	 *
	 * @var \AweBooking\Component\View\Finder
	 */
	protected $finder;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Component\View\Finder $finder
	 */
	public function __construct( Finder $finder ) {
		$this->finder = $finder;
	}

	/**
	 * Get the fully qualified location of the view.
	 *
	 * @param  string $name
	 * @return string
	 *
	 * @throws \Twig_Error_Loader
	 */
	public function find( $name ) {
		if ( file_exists( $name ) ) {
			return $name;
		}

		try {
			return $this->finder->find( $name );
		} catch ( InvalidArgumentException $e ) {
			throw new Twig_Error_Loader( $e->getMessage() );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function exists( $name ) {
		try {
			return (bool) $this->find( $name );
		} catch ( Twig_Error_Loader $e ) {
			return false;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSource( $name ) {
		return file_get_contents( $this->find( $name ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSourceContext( $name ) {
		$path = $this->find( $name );

		return new Twig_Source( file_get_contents( $path ), $name, $path );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCacheKey( $name ) {
		return $this->find( $name );
	}

	/**
	 * {@inheritdoc}
	 */
	public function isFresh( $name, $time ) {
		return filemtime( $this->find( $name ) ) < $time;
	}
}
