<?php
namespace Skeleton;

use RuntimeException;
use Skeleton\Skeleton;

/**
 * A class for generating Custom Post Types.
 *
 * This class is fork from CPT_Core. Thanks for WebDevStudios team.
 */
class Post_Type {
	/**
	 * Singular post type label.
	 *
	 * @var string
	 */
	protected $singular;

	/**
	 * Plural post type label.
	 *
	 * @var string
	 */
	protected $plural;

	/**
	 * CPT name/slug.
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * CPT arguments.
	 *
	 * @var array
	 */
	protected $post_type_args = array();

	/**
	 * WP_Post_Type instance.
	 *
	 * @var WP_Post_Type
	 */
	protected $post_type_object;

	/**
	 * Define a new post type by static method.
	 *
	 * @param string $post_type The post type slug/name.
	 * @param string $singular  The post type singular name for display.
	 * @param string $plural    The post type plural name for display.
	 */
	public static function make( $post_type, $singular, $plural ) {
		return new static( $post_type, $singular, $plural );
	}

	/**
	 * Define a new post type.
	 *
	 * @param string $post_type The post type slug/name.
	 * @param string $singular  The post type singular name for display.
	 * @param string $plural    The post type plural name for display.
	 */
	public function __construct( $post_type, $singular, $plural ) {
		$this->post_type = $post_type;
		$this->singular  = $singular;
		$this->plural    = $plural;

		// Binding CPT instances into the container.
		skeleton()->bind_post_type( $this );
	}

	/**
	 * Return the WP Post Type instance.
	 *
	 * @return \WP_Post_Type If WordPress 4.6+
	 */
	public function get_instance() {
		if ( is_null( $this->post_type_object ) ) {
			throw new RuntimeException( sprintf( esc_html__( '`%s` post type has never been registered before.', 'skeleton' ), $this->post_type ) );
		}

		return $this->post_type_object;
	}

	/**
	 * Set the CPT arguments and init events.
	 *
	 * @param  array $args The CPT arguments.
	 * @return $this
	 */
	public function set( array $args = array() ) {
		$this->post_type_args = $this->parser_args( $args );

		return $this;
	}

	/**
	 * Register a WordPress CPT.
	 *
	 * Triggered by the 'init' action event.
	 *
	 * @access private
	 */
	public function register() {
		$post_type = register_post_type( $this->post_type, $this->post_type_args );

		// If error, yell about it.
		if ( is_wp_error( $post_type ) ) {
			wp_die( $post_type->get_error_message() ); // WPCS: XSS OK.
		}

		// Set registered post type object.
		$this->post_type_object = $post_type;

		// Allow register meta boxes via `$this->create_metabox()` method.
		$this->register_metaboxes();

		add_filter( 'enter_title_here', array( $this, 'enter_title' ) );
		add_filter( 'post_updated_messages', array( $this, 'messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_messages' ), 10, 2 );

		add_filter( 'manage_edit-' . $this->post_type . '_columns', array( $this, 'columns' ) );
		add_filter( 'manage_edit-' . $this->post_type . '_sortable_columns', array( $this, 'sortable_columns' ) );

		// Different column registration for pages/posts.
		$h = isset( $this->post_type_args['hierarchical'] ) && $this->post_type_args['hierarchical'] ? 'pages' : 'posts';
		add_action( "manage_{$h}_custom_column", array( $this, 'columns_display' ), 10, 2 );
	}

	/**
	 * Return passed post type arguments.
	 *
	 * @param  array $args Raw arguments.
	 * @return array
	 */
	protected function parser_args( $args ) {
		// Default labels.
		$labels = array(
			'name'                  => $this->plural,
			'singular_name'         => $this->singular,
			'add_new'               => sprintf( __( 'Add New %s', 'skeleton' ), $this->singular ),
			'add_new_item'          => sprintf( __( 'Add New %s', 'skeleton' ), $this->singular ),
			'edit_item'             => sprintf( __( 'Edit %s', 'skeleton' ), $this->singular ),
			'new_item'              => sprintf( __( 'New %s', 'skeleton' ), $this->singular ),
			'all_items'             => sprintf( __( 'All %s', 'skeleton' ), $this->plural ),
			'view_item'             => sprintf( __( 'View %s', 'skeleton' ), $this->singular ),
			'search_items'          => sprintf( __( 'Search %s', 'skeleton' ), $this->plural ),
			'not_found'             => sprintf( __( 'No %s', 'skeleton' ), $this->plural ),
			'not_found_in_trash'    => sprintf( __( 'No %s found in Trash', 'skeleton' ), $this->plural ),
			'parent_item_colon'     => isset( $args['hierarchical'] ) && $args['hierarchical'] ? sprintf( __( 'Parent %s:', 'skeleton' ), $this->singular ) : null,
			'menu_name'             => $this->plural,
			'insert_into_item'      => sprintf( __( 'Insert into %s', 'skeleton' ), strtolower( $this->singular ) ),
			'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', 'skeleton' ), strtolower( $this->singular ) ),
			'items_list'            => sprintf( __( '%s list', 'skeleton' ), $this->plural ),
			'items_list_navigation' => sprintf( __( '%s list navigation', 'skeleton' ), $this->plural ),
			'filter_items_list'     => sprintf( __( 'Filter %s list', 'skeleton' ), strtolower( $this->plural ) ),
		);

		// Default parameters.
		$defaults = array(
			'labels'             => array(),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'has_archive'        => true,
			'supports'           => array( 'title', 'editor' ),
			'hierarchical'       => false, // Hierarchical causes memory issues - WP loads all records!
		);

		$args = wp_parse_args( $args, $defaults );
		$args['labels'] = wp_parse_args( $args['labels'], $labels );

		return $args;
	}

	/**
	 * Modifies CPT based messages to include CPT labels.
	 *
	 * @access private
	 *
	 * @param  array $messages Array of messages.
	 * @return array
	 */
	public function messages( $messages ) {
		global $post, $post_ID;

		// Just for hack WPCS.
		$post_id = $post_ID;

		$cpt_messages = array(
			0 => '', // Unused. Messages start at index 1.
			2 => __( 'Custom field updated.', 'skeleton' ),
			3 => __( 'Custom field deleted.', 'skeleton' ),
			4 => sprintf( __( '%1$s updated.', 'skeleton' ), $this->singular ),
			/* translators: %s: date and time of the revision */
			5 => isset( $_GET['revision'] ) ? sprintf( __( '%1$s restored to revision from %2$s', 'skeleton' ), $this->singular , wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			7 => sprintf( __( '%1$s saved.', 'skeleton' ), $this->singular ),
		);

		if ( $this->post_type_args['public'] ) {
			$cpt_messages[1] = sprintf( __( '%1$s updated. <a href="%2$s">View %1$s</a>', 'skeleton' ), $this->singular, esc_url( get_permalink( $post_id ) ) );
			$cpt_messages[6] = sprintf( __( '%1$s published. <a href="%2$s">View %1$s</a>', 'skeleton' ), $this->singular, esc_url( get_permalink( $post_id ) ) );
			$cpt_messages[8] = sprintf( __( '%1$s submitted. <a target="_blank" href="%2$s">Preview %1$s</a>', 'skeleton' ), $this->singular, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_id ) ) ) );
			// translators: Publish box date format, see http://php.net/date.
			$cpt_messages[9]  = sprintf( __( '%1$s scheduled for: <strong>%2$s</strong>. <a target="_blank" href="%3$s">Preview %1$s</a>', 'skeleton' ), $this->singular, date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_id ) ) );
			$cpt_messages[10] = sprintf( __( '%1$s draft updated. <a target="_blank" href="%2$s">Preview %1$s</a>', 'skeleton' ), $this->singular, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_id ) ) ) );
		} else {
			$cpt_messages[1] = sprintf( __( '%1$s updated.', 'skeleton' ), $this->singular );
			$cpt_messages[6] = sprintf( __( '%1$s published.', 'skeleton' ), $this->singular );
			$cpt_messages[8] = sprintf( __( '%1$s submitted.', 'skeleton' ), $this->singular );
			// translators: Publish box date format, see http://php.net/date.
			$cpt_messages[9]  = sprintf( __( '%1$s scheduled for: <strong>%2$s</strong>.', 'skeleton' ), $this->singular, date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ) );
			$cpt_messages[10] = sprintf( __( '%1$s draft updated.', 'skeleton' ), $this->singular );
		}

		$messages[ $this->post_type ] = $cpt_messages;
		return $messages;
	}

	/**
	 * Custom bulk actions messages for this post type.
	 *
	 * @access private
	 *
	 * @param  array $bulk_messages  Array of messages.
	 * @param  array $bulk_counts    Array of counts under keys 'updated', 'locked', 'deleted', 'trashed' and 'untrashed'.
	 * @return array
	 */
	public function bulk_messages( $bulk_messages, $bulk_counts ) {
		$bulk_messages[ $this->post_type ] = array(
			'updated'   => sprintf( _n( '%1$s %2$s updated.', '%1$s %3$s updated.', $bulk_counts['updated'], 'skeleton' ), $bulk_counts['updated'], $this->singular, $this->plural ),
			'locked'    => sprintf( _n( '%1$s %2$s not updated, somebody is editing it.', '%1$s %3$s not updated, somebody is editing them.', $bulk_counts['locked'], 'skeleton' ), $bulk_counts['locked'], $this->singular, $this->plural ),
			'deleted'   => sprintf( _n( '%1$s %2$s permanently deleted.', '%1$s %3$s permanently deleted.', $bulk_counts['deleted'], 'skeleton' ), $bulk_counts['deleted'], $this->singular, $this->plural ),
			'trashed'   => sprintf( _n( '%1$s %2$s moved to the Trash.', '%1$s %3$s moved to the Trash.', $bulk_counts['trashed'], 'skeleton' ), $bulk_counts['trashed'], $this->singular, $this->plural ),
			'untrashed' => sprintf( _n( '%1$s %2$s restored from the Trash.', '%1$s %3$s restored from the Trash.', $bulk_counts['untrashed'], 'skeleton' ), $bulk_counts['untrashed'], $this->singular, $this->plural ),
		);

		return $bulk_messages;
	}

	/**
	 * Title entry placeholder text.
	 *
	 * @access private
	 *
	 * @return string
	 */
	public function title() {
		/* translators: %s: singular */
		return sprintf( esc_html__( '%s Title', 'skeleton' ), $this->singular );
	}

	/**
	 * Filter CPT title entry placeholder text.
	 *
	 * @access private
	 *
	 * @param  string $title Original placeholder text.
	 * @return string
	 */
	public function enter_title( $title ) {
		$screen = get_current_screen();

		if ( isset( $screen->post_type ) && $screen->post_type == $this->post_type ) {
			return $this->title();
		}

		return $title;
	}

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
	 * Add meta boxes to this post type.
	 *
	 * @see $this->create_metabox()
	 */
	public function register_metaboxes() {}

	/**
	 * Make a new meta box for this CPT.
	 *
	 * @param  string   $cmb_id   Metabox ID.
	 * @param  callable $callback Metabox arguments.
	 * @return $this
	 */
	public function create_metabox( $cmb_id, $callback = null ) {
		$metabox = new Metabox( $cmb_id, array(
			'object_types' => array( $this->post_type ),
		));

		if ( is_callable( $callback ) ) {
			call_user_func_array( $callback, array( $metabox ) );
		}

		return $metabox;
	}
}
