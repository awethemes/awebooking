<?php
namespace Awethemes\Relationships\Direction;

use Awethemes\Relationships\Relationship;

class Directed {
	/**
	 * The relationship instance.
	 *
	 * @var \Awethemes\Relationships\Relationship
	 */
	protected $relationship;

	/**
	 * The direction (to|from|any).
	 *
	 * @var string
	 */
	protected $direction;

	/**
	 * The direction maps.
	 *
	 * @var array
	 */
	static protected $direction_maps = [
		'current'  => [
			'to'   => 'to',
			'from' => 'from',
			'any'  => 'from',
		],
		'opposite' => [
			'to'   => 'from',
			'from' => 'to',
			'any'  => 'to',
		],
	];

	/**
	 * Constructor.
	 *
	 * @param \Awethemes\Relationships\Relationship $relationship The relationship instance.
	 * @param string                                $direction    The direction.
	 */
	public function __construct( Relationship $relationship, $direction ) {
		static::assert_direction( $direction );

		$this->direction = $direction;

		$this->relationship = $relationship;
	}

	/**
	 * Returns the relationship instance.
	 *
	 * @return \Awethemes\Relationships\Relationship
	 */
	public function get_relationship() {
		return $this->relationship;
	}

	/**
	 * Returns the direction.
	 *
	 * @return string
	 */
	public function get_direction() {
		return $this->direction;
	}

	/**
	 * Returns new instance with flip direction.
	 *
	 * @return static
	 */
	public function flip_direction() {
		$flip_direction = Relationship::DIRECTION_ANY;

		if ( Relationship::DIRECTION_ANY !== $this->direction ) {
			$flip_direction = Relationship::DIRECTION_TO === $this->direction ? Relationship::DIRECTION_FROM : Relationship::DIRECTION_TO;
		}

		return new static( $this->relationship, $flip_direction );
	}

	/**
	 * Returns the current "side".
	 *
	 * @return \Awethemes\Relationships\Side\Side
	 */
	public function get_current() {
		$side = static::$direction_maps['current'][ $this->direction ];

		return $this->get_relationship()->get_side( $side );
	}

	/**
	 * Returns the opposite "side".
	 *
	 * @return \Awethemes\Relationships\Side\Side
	 */
	public function get_opposite() {
		$side = static::$direction_maps['opposite'][ $this->direction ];

		return $this->get_relationship()->get_side( $side );
	}

	/**
	 * Assert direction is valid.
	 *
	 * @param string $direction The direction.
	 */
	protected static function assert_direction( $direction ) {
		$dirs = [ Relationship::DIRECTION_ANY, Relationship::DIRECTION_FROM, Relationship::DIRECTION_TO ];

		if ( ! in_array( $direction, $dirs ) ) {
			throw new \OutOfBoundsException( 'The direction must be one of:' . implode( ', ', $dirs ) );
		}
	}
}
