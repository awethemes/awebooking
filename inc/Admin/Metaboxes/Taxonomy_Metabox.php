<?php
namespace AweBooking\Admin\Metaboxes;

use Skeleton\Metabox;

abstract class Taxonomy_Metabox {
	/**
	 * Taxonomy ID to register meta-boxes.
	 *
	 * @var string
	 */
	protected $taxonomy = 'category';

	/**
	 * Default metabox.
	 *
	 * @var Skeleton\Metabox
	 */
	protected $default_metabox;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->default_metabox = $this->create_metabox( $this->taxonomy );

		$this->register();
	}

	/**
	 * Here you can register metabox(es) or fields.
	 *
	 * @return void
	 */
	public function register() {}

	/**
	 * Add a field to default metabox of this taxonomy.
	 *
	 * @param  array $args Field args.
	 * @return int|false
	 */
	protected function add_field( array $args ) {
		return $this->get_default_metabox()->add_field( $args );
	}

	/**
	 * Get default metabox for this taxonomy.
	 *
	 * @return Skeleton\Metabox
	 */
	protected function get_default_metabox() {
		return $this->default_metabox;
	}

	/**
	 * Create a new metabox.
	 *
	 * @param string $cmb_id Metabox ID.
	 * @param array  $args   Metabox args.
	 */
	protected function create_metabox( $cmb_id, array $args = [] ) {
		return (new Metabox( $cmb_id ))
			->set( $args )
			->show_on_term( $this->taxonomy );
	}
}
