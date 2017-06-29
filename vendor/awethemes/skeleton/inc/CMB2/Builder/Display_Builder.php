<?php
namespace Skeleton\CMB2\Builder;

use Skeleton\Metabox;

class Display_Builder implements Display_Builder_Interface {
	/**
	 * Metabox instance.
	 *
	 * @var string
	 */
	protected $metabox;

	/**
	 * Constructor of class.
	 *
	 * @param Metabox $metabox Metabox instance object.
	 */
	public function __construct( Metabox $metabox ) {
		$this->metabox = $metabox;
	}

	public function only_ids( $post_id ) {
		$this->metabox->set_prop( 'show_on', array( 'key' => 'id', 'value' => $post_id ) );
	}

	public function only_page_templates( $templates ) {
		$this->metabox->set_prop( 'show_on', array( 'key' => 'page-template', 'value' => $templates ) );
	}
}
