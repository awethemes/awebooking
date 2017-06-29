<?php
namespace AweBooking\Admin\List_Tables;

abstract class Post_Type_Abstract {
	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * List table primary column.
	 *
	 * @var string
	 */
	protected $primary_column;

	/**
	 * Post type admin handler.
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
		add_filter( 'manage_edit-' . $this->post_type . '_columns', array( $this, 'columns' ) );
		add_filter( 'manage_edit-' . $this->post_type . '_sortable_columns', array( $this, 'sortable_columns' ) );
		add_action( 'manage_' . $this->post_type . '_posts_custom_column', array( $this, 'columns_display' ), 10, 2 );

		// Set list table primary column.
		add_filter( 'list_table_primary_column', array( $this, 'primary_column' ), 10, 2 );

		$this->init();
	}

	/**
	 * Init somethings hooks.
	 *
	 * @access private
	 */
	public function init() {}

	/**
	 * Registers admin columns to display.
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
	 * Registers which columns are sortable.
	 *
	 * @access private
	 *
	 * @param  array $sortable_columns Array of registered column keys => data-identifier.
	 * @return array
	 */
	public function sortable_columns( $sortable_columns ) {
		return $sortable_columns;
	}

	/**
	 * Handles admin column display.
	 *
	 * @access private
	 *
	 * @param string $column  The name of the column to display.
	 * @param int    $post_id Current post ID.
	 */
	public function columns_display( $column, $post_id ) {}

	/**
	 * Set list table primary column.
	 *
	 * @access private
	 *
	 * @param string $default   Column name default for the specific list table.
	 * @param string $screen_id Screen ID for specific list table.
	 * @return string
	 */
	public function primary_column( $default, $screen_id ) {
		if ( is_null( $this->primary_column ) ) {
			return $default;
		}

		if ( 'edit-' . $this->post_type === $screen_id ) {
			return $this->primary_column;
		}

		return $default;
	}
}
