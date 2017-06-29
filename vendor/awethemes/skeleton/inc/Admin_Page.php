<?php
namespace Skeleton;

use CMB2_Boxes;
use CMB2_hookup;
use LogicException;

/**
 * Creating custom options panels in WordPress.
 */
class Admin_Page extends CMB2\CMB2 {
	/**
	 * Options page ID.
	 *
	 * @var string
	 */
	public $page_id;

	/**
	 * Name of the query-string argument for the admin page.
	 *
	 * @var string
	 */
	public $menu_slug;

	/**
	 * Capability needed to view the page menu item.
	 *
	 * @var string
	 */
	public $capability = 'manage_options';

	/**
	 * The text to be displayed in the title tags of the page.
	 *
	 * @var string
	 */
	public $page_title;

	/**
	 * The text to be used for the menu.
	 *
	 * @var string
	 */
	public $menu_title;

	/**
	 * The slug name for the parent menu.
	 *
	 * @see add_submenu_page()
	 *
	 * @var string
	 */
	public $parent_slug;

	/**
	 * The URL to the icon to be used for page menu.
	 *
	 * @see add_menu_page()
	 *
	 * @var string
	 */
	public $icon_url;

	/**
	 * The position in the page menu.
	 *
	 * @see add_menu_page()
	 *
	 * @var interger
	 */
	public $position;

	/**
	 * Set custom render callback.
	 *
	 * @var callable
	 */
	public $render_callback;

	/**
	 * Holds configurable array of strings.
	 *
	 * @var array
	 */
	public $strings = array();

	/**
	 * Options Page hook.
	 *
	 * @var string
	 */
	protected $page_hook;

	/**
	 * Make a new page settings using static method.
	 *
	 * @param string $page_id Page Settings ID.
	 */
	public static function make( $page_id ) {
		return new static( $page_id );
	}

	/**
	 * Make a new page settings.
	 *
	 * @param string $page_id Page Settings ID.
	 */
	public function __construct( $page_id, Menu_Page $parent_menu = null ) {
		$this->page_id = sanitize_key( $page_id );
		$this->menu_slug = $this->page_id;

		$metabox_args = array(
			'id'         => $this->page_id,
			'show_on'    => array( 'options-page' => $this->menu_slug ),
			'hookup'     => false, // Disable hookup, Use cmb2_metabox_form().
			'cmb_styles' => false, // Disable CMB2 load styles, we will load this styles late.
		);

		if ( CMB2_Boxes::get( $metabox_args['id'] ) ) {
			throw new LogicException( "A metabox with id `{$metabox_args['id']}` has been registered." );
		}

		parent::__construct( $metabox_args, $this->page_id );
		$this->object_type( 'options-page' );

		if ( $parent_menu ) {
			$this->parent_slug = $parent_menu->get_topmenu();
		}

		// Set default style for navigation.
		$this->render->navigation_class = 'wp-clearfix cmb2-nav-default';
	}

	/**
	 * Set page settings arguments.
	 *
	 * @param array $args Settings arguments.
	 */
	public function set( $args ) {
		$keys = array_keys( get_object_vars( $this ) );
		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->{$key} = $args[ $key ];
			}
		}

		// Re-setting show_on property.
		if ( isset( $args['menu_slug'] ) ) {
			$this->set_prop( 'show_on', array(
				'options-page' => $this->menu_slug,
			));
		}

		$this->strings = wp_parse_args( $this->strings, array(
			'page_title'  => null,
			'save_button' => esc_html__( 'Save Changes', 'skeleton' ),
			'updated'     => esc_html__( 'Settings updated.', 'skeleton' ),
			'error'       => esc_html__( 'One or more error occurred.', 'skeleton' ),
		));

		if ( ! is_callable( $this->render_callback ) ) {
			$this->render_callback = array( $this, 'display' );
		}

		return $this->init();
	}

	/**
	 * Init page hooks.
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		// Hook in our save notices.
		add_action( "cmb2_save_options-page_fields_{$this->prop( 'id' )}", array( $this, 'settings_notices' ), 10, 3 );

		return $this;
	}

	/**
	 * Register this setting to WP.
	 *
	 * @access private
	 */
	public function register_settings() {
		register_setting( $this->page_id, $this->page_id );
	}

	/**
	 * Register settings notices for display.
	 *
	 * @param  int   $object_id Option key.
	 * @param  array $updated   Array of updated fields.
	 * @param  CMB2  $cmb2      CMB2 instance.
	 *
	 * @access private
	 */
	public function settings_notices( $object_id, $updated, $cmb2 ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( $object_id !== $this->page_id || empty( $updated ) ) {
			return;
		}

		if ( $cmb2->get_errors() ) {
			add_settings_error( $this->page_id . '-notices', '', $this->strings['error'], 'error' );
		} else {
			add_settings_error( $this->page_id . '-notices', '', $this->strings['updated'], 'updated' );
		}

		settings_errors( $this->page_id . '-notices' );
	}

	/**
	 * Adds submenu page if there are plugin actions to take.
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_menu_page
	 * @see https://developer.wordpress.org/reference/functions/add_submenu_page
	 *
	 * @access private
	 */
	public function admin_menu() {
		if ( $this->parent_slug ) {
			$this->page_hook = call_user_func( 'add_submenu_page', $this->parent_slug, $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, $this->render_callback );
		} else {
			$this->page_hook = call_user_func( 'add_menu_page', $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, $this->render_callback, $this->icon_url, $this->position );
		}

		// Include CMB CSS in the head to avoid FOUC.
		add_action( "admin_print_styles-{$this->page_hook}", array( $this, 'enqueue_script' ) );
	}

	/**
	 * Enqueue CMB2 and our styles, scripts.
	 *
	 * @access private
	 */
	public function enqueue_script() {
		CMB2_hookup::enqueue_cmb_css();

		wp_enqueue_style( 'skeleton-cmb2' );
		wp_enqueue_script( 'skeleton-cmb2' );
	}

	/**
	 * Admin page markup. Mostly handled by CMB2.
	 *
	 * @access private
	 */
	public function display() {
		$page_title = ! is_null( $this->strings['page_title'] ) ? $this->strings['page_title'] : get_admin_page_title();

		?><div id="page-settings-<?php echo esc_attr( $this->page_id ); ?>" class="wrap cmb2-options-page">
			<?php if ( $page_title ) : ?>
				<h1><?php echo esc_html( $page_title ); ?></h1>
			<?php endif ?>

			<?php cmb2_metabox_form( $this, $this->page_id, array( 'save_button' => $this->strings['save_button'] ) ); ?>
		</div><?php
	}
}
