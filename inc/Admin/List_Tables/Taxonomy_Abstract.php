<?php
namespace AweBooking\Admin\List_Tables;

abstract class Taxonomy_Abstract {
	/**
	 * Taxonomy slug.
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Taxonomy admin handler.
	 */
	public function __construct() {
		$this->register();
	}

	/**
	 * Register hooks.
	 *
	 * @access private
	 */
	public function register() {
		// Manager columns and display.
		add_filter( "manage_edit-{$this->taxonomy}_columns", array( $this, 'columns' ), 10 );
		add_action( "manage_{$this->taxonomy}_custom_column", array( $this, 'columns_display' ), 10, 3 );

		$this->init();
	}

	/**
	 * Init somethings hooks.
	 *
	 * @access private
	 */
	public function init() {}

	/**
	 * Registers columns to display in the terms list table.
	 *
	 * @access private
	 *
	 * @param  array $columns Array of registered column names/labels.
	 * @return array
	 */
	public function columns( $columns ) {
		return $columns;
	}

	/**
	 * Handles display columns in the terms list table.
	 *
	 * @access private
	 *
	 * @param string $content     The column content.
	 * @param string $column_name Name of the column.
	 * @param int    $term_id     Term ID.
	 */
	public function columns_display( $content, $column_name, $term_id ) {}
}
