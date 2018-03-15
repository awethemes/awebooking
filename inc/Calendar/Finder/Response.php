<?php
namespace AweBooking\Calendar\Finder;

use AweBooking\Support\Collection;
use AweBooking\Calendar\Period\Period;
use AweBooking\Calendar\Resource\Resource_Interface;

class Response {
	/* Constants */
	const VALID_STATE   = 'valid_state';
	const INVALID_STATE = 'invalid_state';

	/**
	 * The period instance.
	 *
	 * @var \AweBooking\Calendar\Period\Period
	 */
	protected $period;

	/**
	 * All resources.
	 *
	 * @var \AweBooking\Calendar\Resource\Resources
	 */
	protected $resources;

	/**
	 * The resources included.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $included;

	/**
	 * The resources has been excluded.
	 *
	 * @var \AweBooking\Support\Collection
	 */
	protected $excluded;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Calendar\Period\Period      $period    The period of the finding.
	 * @param \AweBooking\Calendar\Resource\Resources $resources All resources.
	 */
	public function __construct( Period $period, $resources ) {
		$this->period = $period;
		$this->resources = $resources;

		$this->included = new Collection;
		$this->excluded = new Collection;
	}

	/**
	 * The the period instance.
	 *
	 * @return \AweBooking\Calendar\Period\Period
	 */
	public function get_period() {
		return $this->period;
	}

	/**
	 * Get the resources.
	 *
	 * @return \AweBooking\Calendar\Resource\Resources
	 */
	public function get_resources() {
		return $this->resources;
	}

	/**
	 * Get resources included.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_included() {
		return $this->included;
	}

	/**
	 * Get resources has been excluded.
	 *
	 * @return \AweBooking\Support\Collection
	 */
	public function get_excluded() {
		return $this->excluded;
	}

	/**
	 * Determines if a resource remain the the matches.
	 *
	 * @param  \AweBooking\Model\Room $resource The resource_unit instance.
	 * @return bool
	 */
	public function remain( $resource ) {
		$resource = ( $resource instanceof Resource_Interface )
			? $resource->get_uid()
			: (int) $resource;

		return $this->included->has( $resource );
	}

	/**
	 * Add a resource to the included.
	 *
	 * @param  \AweBooking\Calendar\Resource\Resource_Interface $resource The resource to add.
	 * @param  string                                           $reason   The reason why added into this.
	 * @return bool
	 */
	public function add_match( Resource_Interface $resource, $reason ) {
		$index = $resource->get_id();

		// Can't add a unknown resource or has been excluded.
		if ( ! $this->resources->has( $index ) || $this->excluded->has( $index ) ) {
			return false;
		}

		$this->included->put( $index, compact( 'resource', 'reason' ) );

		return true;
	}

	/**
	 * Exclude a resource from the availability.
	 *
	 * @param  \AweBooking\Calendar\Resource\Resource_Interface $resource   The resource to add.
	 * @param  string                                           $reason     The reason why added into this.
	 * @param  \AweBooking\Calendar\Finder\Constraint           $constraint The constraint for the reason.
	 * @return bool
	 */
	public function add_miss( Resource_Interface $resource, $reason, Constraint $constraint = null ) {
		$index = $resource->get_id();

		// Can't add a unknown resource or ready in matches.
		if ( ! $this->resources->has( $index ) || $this->included->has( $resource->get_id() ) ) {
			return false;
		}

		$this->excluded->put( $index, compact( 'resource', 'reason', 'constraint' ) );

		return true;
	}

	/**
	 * Reject a resource from the matches.
	 *
	 * @param  \AweBooking\Calendar\Resource\Resource_Interface $resource   The resource to reject.
	 * @param  string                                           $reason     The reason why reject this.
	 * @param  \AweBooking\Calendar\Finder\Constraint           $constraint The constraint for the reason.
	 * @return bool
	 */
	public function reject( Resource_Interface $resource, $reason, Constraint $constraint = null ) {
		$index = $resource->get_id();

		if ( ! $this->included->has( $index ) ) {
			return false;
		}

		// Remove resource in matches before reject.
		unset( $this->included[ $index ] );

		return $this->add_miss( $resource, $reason, $constraint );
	}

	/**
	 * Apply the constraints to this response.
	 *
	 * @param  array $constraints \AweBooking\Calendar\Finder\Constraint[].
	 * @return $this
	 */
	public function apply_constraints( $constraints ) {
		$constraints = ! is_array( $constraints ) ? [ $constraints ] : $constraints;

		foreach ( $constraints as $constraint ) {
			if ( $constraint instanceof Constraint ) {
				$constraint->apply( $this );
			}
		}

		return $this;
	}
}
