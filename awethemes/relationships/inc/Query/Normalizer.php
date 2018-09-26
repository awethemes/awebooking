<?php
namespace Awethemes\Relationships\Query;

use Awethemes\Relationships\Manager;

class Normalizer {
	/**
	 * The relationship manager.
	 *
	 * @var \Awethemes\Relationships\Manager
	 */
	protected $manager;

	/**
	 * Constructor.
	 *
	 * @param \Awethemes\Relationships\Manager $manager The relationship manager.
	 */
	public function __construct( Manager $manager ) {
		$this->manager = $manager;
	}

	/**
	 * Normalize relationship query args.
	 *
	 * @param array $args Query arguments.
	 * @return \Awethemes\Relationships\Query\Normalized|null
	 */
	public function normalize( $args ) {
		if ( ! isset( $args['name'] ) || ! $relation = $this->manager->get( $args['name'] ) ) {
			return null;
		}

		$direction = isset( $args['from'] ) ? 'from' : 'to';

		return new Normalized( $relation, $direction, $args[ $direction ] );
	}
}
