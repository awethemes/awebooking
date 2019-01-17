<?php

namespace AweBooking\Admin\List_Tables;

abstract class Abstract_List_Table {
	/**
	 * The post type name.
	 *
	 * @var string
	 */
	protected $list_table = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$wp_post_type = get_post_type_object( $this->list_table );

		if ( ! is_null( $wp_post_type ) ) {
			add_filter( 'view_mode_post_types', [ $this, 'disable_view_mode' ] );
			add_action( 'restrict_manage_posts', [ $this, 'restrict_manage_posts' ] );
			add_filter( 'request', [ $this, 'request_query' ] );
			add_filter( ( $wp_post_type->hierarchical ? 'page_row_actions' : 'post_row_actions' ), [ $this, 'row_actions' ], 10, 2 );
			add_filter( 'default_hidden_columns', [ $this, 'default_hidden_columns' ], 10, 2 );
			add_filter( 'list_table_primary_column', [ $this, 'list_table_primary_column' ], 10, 2 );
			add_filter( 'manage_edit-' . $this->list_table . '_sortable_columns', [ $this, 'define_sortable_columns' ] );
			add_filter( 'manage_' . $this->list_table . '_posts_columns', [ $this, 'define_columns' ] );
			add_filter( 'bulk_actions-edit-' . $this->list_table, [ $this, 'define_bulk_actions' ] );
			add_action( 'manage_' . $this->list_table . '_posts_custom_column', [ $this, 'render_columns' ], 10, 2 );
			add_filter( 'handle_bulk_actions-edit-' . $this->list_table, [ $this, 'handle_bulk_actions' ], 10, 3 );
		}
	}

	/**
	 * Removes this type from list of post types that support "View Mode" switching.
	 *
	 * View mode is seen on posts where you can switch between list or excerpt. Our post types don't support
	 * it, so we want to hide the useless UI from the screen options tab.
	 *
	 * @param  array $post_types Array of post types supporting view mode.
	 * @return array             Array of post types supporting view mode, without this type.
	 *
	 * @access private
	 */
	public function disable_view_mode( $post_types ) {
		unset( $post_types[ $this->list_table ] );

		return $post_types;
	}

	/**
	 * See if we should render search filters or not.
	 *
	 * @access private
	 */
	public function restrict_manage_posts() {
		global $typenow;

		if ( $this->list_table === $typenow ) {
			$this->render_filters();
		}
	}

	/**
	 * Render any custom filters and search inputs for the list table.
	 *
	 * @return void
	 */
	protected function render_filters() {}

	/**
	 * Handle any filters.
	 *
	 * @param  array $query_vars Query vars.
	 * @return array
	 *
	 * @access private
	 */
	public function request_query( $query_vars ) {
		global $typenow;

		if ( $this->list_table === $typenow ) {
			return $this->query_filters( $query_vars );
		}

		return $query_vars;
	}

	/**
	 * Handle any custom filters.
	 *
	 * @param  array $query_vars Query vars.
	 * @return array
	 */
	protected function query_filters( $query_vars ) {
		return $query_vars;
	}

	/**
	 * Set row actions.
	 *
	 * @param  array    $actions Array of actions.
	 * @param  \WP_Post $post Current post object.
	 * @return array
	 *
	 * @access private
	 */
	public function row_actions( $actions, $post ) {
		if ( $this->list_table === $post->post_type ) {
			return $this->get_row_actions( $actions, $post );
		}

		return $actions;
	}

	/**
	 * Get row actions to show in the list table.
	 *
	 * @param  array    $actions Array of actions.
	 * @param  \WP_Post $post Current post object.
	 * @return array
	 */
	protected function get_row_actions( $actions, $post ) {
		return $actions;
	}

	/**
	 * Adjust which columns are displayed by default.
	 *
	 * @param  array  $hidden Current hidden columns.
	 * @param  object $screen Current screen.
	 * @return array
	 *
	 * @access private
	 */
	public function default_hidden_columns( $hidden, $screen ) {
		if ( isset( $screen->id ) && 'edit-' . $this->list_table === $screen->id ) {
			$hidden = array_merge( $hidden, $this->define_hidden_columns() );
		}

		return $hidden;
	}

	/**
	 * Define hidden columns.
	 *
	 * @return array
	 */
	protected function define_hidden_columns() {
		return [];
	}

	/**
	 * Set list table primary column.
	 *
	 * @param  string $default Default value.
	 * @param  string $screen_id Current screen ID.
	 * @return string
	 *
	 * @access private
	 */
	public function list_table_primary_column( $default, $screen_id ) {
		if ( 'edit-' . $this->list_table === $screen_id && $primary_column = $this->get_primary_column() ) {
			return $primary_column;
		}

		return $default;
	}

	/**
	 * Define primary column.
	 *
	 * @return string
	 */
	protected function get_primary_column() {
		return '';
	}

	/**
	 * Define which columns are sortable.
	 *
	 * @param  array $columns Existing columns.
	 * @return array
	 *
	 * @access private
	 */
	public function define_sortable_columns( $columns ) {
		return $columns;
	}

	/**
	 * Define which columns to show on this screen.
	 *
	 * @param  array $columns Existing columns.
	 * @return array
	 *
	 * @access private
	 */
	public function define_columns( $columns ) {
		return $columns;
	}

	/**
	 * Define bulk actions.
	 *
	 * @param  array $actions Existing actions.
	 * @return array
	 *
	 * @access private
	 */
	public function define_bulk_actions( $actions ) {
		return $actions;
	}

	/**
	 * Pre-fetch any data for the row each column has access to it.
	 *
	 * @param int $post_id Post ID being shown.
	 */
	protected function prepare_row_data( $post_id ) {}

	/**
	 * Render individual columns.
	 *
	 * @param string $column Column ID to render.
	 * @param int    $post_id Post ID being shown.
	 *
	 * @access private
	 */
	public function render_columns( $column, $post_id ) {
		$this->prepare_row_data( $post_id );

		$method = 'display_' . $column . '_column';
		if ( method_exists( $this, $method ) ) {
			$this->{$method}( $post_id );
		}
	}

	/**
	 * Handle bulk actions.
	 *
	 * @param  string $redirect_to URL to redirect to.
	 * @param  string $action      Action name.
	 * @param  array  $ids         List of ids.
	 * @return string
	 *
	 * @access private
	 */
	public function handle_bulk_actions( $redirect_to, $action, $ids ) {
		return esc_url_raw( $redirect_to );
	}
}
