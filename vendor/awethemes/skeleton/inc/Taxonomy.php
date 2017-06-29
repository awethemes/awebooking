<?php

namespace Skeleton;

use CMB2_Boxes;
use RuntimeException;
use Skeleton\WP_Option;
use Skeleton\Skeleton;

/**
 * Taxonomy registration starter class.
 */
class Taxonomy {
	/**
	 * Singlur Taxonomy label.
	 *
	 * @var string
	 */
	protected $singular;

	/**
	 * Plural Taxonomy label.
	 *
	 * @var string
	 */
	protected $plural;

	/**
	 * The name of the taxonomy.
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Name of the object type for the taxonomy object.
	 *
	 * @var array
	 */
	protected $object_types;

	/**
	 * Taxonomy registration arguments.
	 *
	 * @var array
	 */
	protected $taxonomy_args = array();

	/**
	 * Taxonomy registered object.
	 *
	 * @var object
	 */
	protected $taxonomy_object;

	/**
	 * WP_Option store permalink settings.
	 *
	 * @var Skeleton\WP_Option
	 */
	protected $wp_option;

	/**
	 * Create a taxonomy by static method.
	 *
	 * @param string       $taxonomy     The name of the taxonomy.
	 * @param string|array $object_types Name of the object type for the taxonomy object.
	 * @param string       $singular     Singular taxonomy name.
	 * @param string       $plural       Plural taxonomy name.
	 */
	public static function make( $taxonomy, $object_types, $singular, $plural ) {
		return new static( $taxonomy, $object_types, $singular, $plural );
	}

	/**
	 * Create a taxonomy.
	 *
	 * @param string       $taxonomy     The name of the taxonomy.
	 * @param string|array $object_types Name of the object type for the taxonomy object.
	 * @param string       $singular     Singular taxonomy name.
	 * @param string       $plural       Plural taxonomy name.
	 */
	public function __construct( $taxonomy, $object_types, $singular, $plural ) {
		$this->taxonomy = $taxonomy;
		$this->object_types = (array) $object_types;

		$this->plural = $plural;
		$this->singular = $singular;

		$this->wp_option = new WP_Option( 'skeleton_permalinks' );
	}

	/**
	 * Return the WP Taxonomy instance.
	 *
	 * @return object|\WP_Taxonomy If WordPress 4.7+
	 */
	public function get_instance() {
		if ( is_null( $this->taxonomy_object ) ) {
			throw new RuntimeException( sprintf( esc_html__( '`%s` taxonomy has never been registered before.', 'skeleton' ), $this->taxonomy ) );
		}

		return $this->taxonomy_object;
	}

	/**
	 * Set the taxonomy arguments.
	 *
	 * @param  array $taxonomy_args The taxonomy arguments.
	 * @return $this
	 */
	public function set( array $taxonomy_args = array() ) {
		$this->taxonomy_args = $this->parser_args( $taxonomy_args );

		// add_action( 'admin_init', array( $this, 'init_permalink_settings' ) );
		// add_action( 'current_screen', array( $this, 'save_permalink_settings' ) );

		if ( doing_filter( 'skeleton/init' ) ) {
			// If inside an `skeleton/init` action, simply call the register method.
			$this->register();
		} else {
			// Out of an `skeleton/init` action, call the hook.
			add_action( 'skeleton/init', array( $this, 'register' ) );
		}

		return $this;
	}

	/**
	 * Initialize the permalink settings.
	 */
	public function init_permalink_settings() {
		$id = sprintf( 'skeleton-taxonomy-%s-base', $this->taxonomy );
		$title = sprintf( esc_html__( '%s base', 'skeleton' ), $this->singular );

		add_settings_field( $id, $title, array( $this, 'permalink_slug_input' ), 'permalink', 'optional' );
	}

	/**
	 * Show a slug input box.
	 *
	 * @since 3.9.2
	 * @access  public
	 * @param  array $args The argument.
	 */
	public function permalink_slug_input( $args ) {
		$permalinks     = get_option( 'avada_permalinks' );
		$permalink_base = $args['taxonomy'] . '_base';
		$input_name     = 'skeleton_' . $args['taxonomy'] . '_base';
		$placeholder    = $args['taxonomy'];
		?>
		<input name="<?php echo $input_name; ?>" type="text" class="regular-text code" value="<?php echo ( isset( $permalinks[ $permalink_base ] ) ) ? esc_attr( $permalinks[ $permalink_base ] ) : ''; ?>" placeholder="<?php echo esc_attr( $placeholder ) ?>" />
		<?php
	}

	/**
	 * Save the permalink settings.
	 *
	 * @since 3.9.2
	 */
	public function save_permalink_settings() {
		$screen = get_current_screen();

		if ( ! $screen || 'options-permalink' !== $screen->id ) {
			return;
		}

		$input_id = 'skeleton_' . $this->taxonomy . '_base';

		if ( isset( $_POST['permalink_structure'] ) && isset( $_POST[ $input_id ] ) ) {
			check_admin_referer( 'update-permalink' );

			$a = $_POST[ $input_id ];

			(new WP_Option('skeleton_permalinks'))->set($input_id, $a);
		}
	}

	/**
	 * Actually registers our Taxonomy with the merged arguments.
	 *
	 * @access private
	 */
	public function register() {
		global $wp_taxonomies;

		// Register taxonomy.
		$result = register_taxonomy( $this->taxonomy, $this->object_types, $this->taxonomy_args );

		// If error, yell about it.
		if ( is_wp_error( $result ) ) {
			wp_die( $result->get_error_message() ); // WPCS: XSS OK.
		}

		$this->meta_boxes();

		// Success. Set args to what WP returns.
		$this->taxonomy_object = $wp_taxonomies[ $this->taxonomy ];

		// Add this taxonomy to taxonomies container.
		skeleton()->bind_taxonomy( $this );
	}

	/**
	 * Gets the passed in arguments combined with our defaults.
	 *
	 * @param  array $taxonomy_args Taxonomy arguments.
	 * @return array
	 */
	protected function parser_args( array $taxonomy_args ) {
		// Hierarchical check that will be used multiple times below.
		$hierarchical = true;
		if ( isset( $taxonomy_args['hierarchical'] ) ) {
			$hierarchical = (bool) $taxonomy_args['hierarchical'];
		}

		// Default labels.
		$labels = array(
			'name'                       => $this->plural,
			'singular_name'              => $this->singular,
			'search_items'               => sprintf( __( 'Search %s', 'skeleton' ), $this->plural ),
			'all_items'                  => sprintf( __( 'All %s', 'skeleton' ), $this->plural ),
			'edit_item'                  => sprintf( __( 'Edit %s', 'skeleton' ), $this->singular ),
			'view_item'                  => sprintf( __( 'View %s', 'skeleton' ), $this->singular ),
			'update_item'                => sprintf( __( 'Update %s', 'skeleton' ), $this->singular ),
			'add_new_item'               => sprintf( __( 'Add New %s', 'skeleton' ), $this->singular ),
			'new_item_name'              => sprintf( __( 'New %s Name', 'skeleton' ), $this->singular ),
			'not_found'                  => sprintf( __( 'No %s found.', 'skeleton' ), $this->plural ),
			'no_terms'                   => sprintf( __( 'No %s', 'skeleton' ), $this->plural ),

			// Hierarchical stuff.
			'parent_item'       => $hierarchical ? sprintf( __( 'Parent %s', 'skeleton' ), $this->singular ) : null,
			'parent_item_colon' => $hierarchical ? sprintf( __( 'Parent %s:', 'skeleton' ), $this->singular ) : null,

			// Non-hierarchical stuff.
			'popular_items'              => $hierarchical ? null : sprintf( __( 'Popular %s', 'skeleton' ), $this->plural ),
			'separate_items_with_commas' => $hierarchical ? null : sprintf( __( 'Separate %s with commas', 'skeleton' ), $this->plural ),
			'add_or_remove_items'        => $hierarchical ? null : sprintf( __( 'Add or remove %s', 'skeleton' ), $this->plural ),
			'choose_from_most_used'      => $hierarchical ? null : sprintf( __( 'Choose from the most used %s', 'skeleton' ), $this->plural ),
		);

		$defaults = array(
			'labels'            => array(),
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug' => $this->taxonomy,
				'hierarchical' => $hierarchical,
			),
		);

		$taxonomy_args = wp_parse_args( $taxonomy_args, $defaults );
		$taxonomy_args['labels'] = wp_parse_args( $taxonomy_args['labels'], $labels );

		return $taxonomy_args;
	}

	/**
	 * Add meta boxes to this post type.
	 *
	 * @see $this->add_meta_box()
	 * @see \Skeleton\Metabox
	 */
	public function meta_boxes() {}

	/**
	 * Make a new meta box for this taxonomy.
	 *
	 * Note: Use this method will create a metabox with ID `awethemes/taxonomy/{$cmb_id}`.
	 *
	 * @param  string   $cmb_id   Metabox ID.
	 * @param  callable $callback Metabox arguments.
	 * @return $this
	 */
	public function add_meta_box( $cmb_id, $callback = null ) {
		if ( 'default' === $cmb_id ) {
			$cmb_id = $this->taxonomy . '/default';
		} else {
			$cmb_id = $this->taxonomy . '/' . $cmb_id;
		}

		$metabox = new Metabox( $cmb_id, array(
			'object_types' => array( 'term' ),
			'taxonomies'   => array( $this->taxonomy ),
		));

		if ( is_callable( $callback ) ) {
			call_user_func_array( $callback, array( $metabox ) );
		}

		return $metabox;
	}

	/**
	 * Add a field to default metabox of this taxonomy.
	 *
	 * @param  array $field    Metabox field config array.
	 * @param  int   $position Optional, position of metabox.
	 * @return int|false
	 */
	public function add_field( array $field, $position = 0 ) {
		return $this->default_metabox()->add_field( $field, $position );
	}

	/**
	 * Add a group field to default metabox of this taxonomy.
	 *
	 * @param  array    $id       Group ID.
	 * @param  callable $callback Group builder callback.
	 * @param  int      $position Optional, position of group.
	 * @return \Skeleton\CMB2Builder\GroupBuilder
	 */
	public function add_group( $id, $callback = null, $position = 0 ) {
		return $this->default_metabox()->add_group( $id, $callback, $position );
	}

	/**
	 * Get default metabox for this taxonomy.
	 *
	 * @return \Skeleton\Metabox
	 */
	protected function default_metabox() {
		$metabox = CMB2_Boxes::get( $this->taxonomy . '/default' );

		if ( $metabox ) {
			return $metabox;
		}

		return $this->add_meta_box( 'default', function ( $mb ) {
			$mb->set_priority( 0 );
			$mb->vertical_tabs( false );
		});
	}
}
