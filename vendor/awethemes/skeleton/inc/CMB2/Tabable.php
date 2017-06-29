<?php
namespace Skeleton\CMB2;

abstract class Tabable implements Tabable_Interface {
	/**
	 * Unique identifier.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Tab icon to show in the UI.
	 *
	 * @var string
	 */
	public $icon = '';

	/**
	 * Title of the tab to show in UI.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Description to show in the UI.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Priority of the tab.
	 *
	 * @var integer
	 */
	public $priority = 10;

	/**
	 * Capability required for the tab.
	 *
	 * @var string
	 */
	public $capability = 'edit_theme_options';

	/**
	 * Theme feature support for the tab.
	 *
	 * @var string|array
	 */
	public $theme_supports = '';

	/**
	 * Metabox tab show callback.
	 *
	 * @var callable
	 */
	public $show_on_cb;

	/**
	 * CMB2 instance.
	 *
	 * @var CMB2
	 */
	protected $cmb2;

	/**
	 * Constructor.
	 *
	 * Any supplied $args override class property defaults.
	 *
	 * @param CMB2   $cmb2 CMB2 instance.
	 * @param string $id   An specific ID for the tab.
	 * @param array  $args Tab arguments.
	 */
	public function __construct( CMB2 $cmb2, $id, $args = array() ) {
		$this->id = $id;
		$this->cmb2 = $cmb2;

		if ( ! empty( $args ) ) {
			$this->set( $args );
		}
	}

	/**
	 * Set tab properties.
	 *
	 * @param  array $args The tab properties.
	 * @return $this
	 */
	public function set( $args = array() ) {
		$keys = array_keys( get_object_vars( $this ) );

		foreach ( $keys as $key ) {
			// Prevent set id property.
			if ( 'id' === $key ) {
				continue;
			}

			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		return $this;
	}

	/**
	 * Return tab unique ID.
	 *
	 * @return string
	 */
	public function uniqid() {
		return sprintf( 'cmb2-%s-section-%s', sanitize_key( $this->cmb2->cmb_id ), $this->id );
	}

	/**
	 * Set a callback to determine showing the tab.
	 *
	 * @param  callable $callback Callback functions, closure, etc...
	 * @return $this
	 */
	public function show_on( $callback ) {
		$this->show_on_cb = $callback;
		return $this;
	}

	/**
	 * Determine whether this tab should show.
	 *
	 * @return bool
	 */
	public function should_show() {
		// Default to showing the tab.
		$show = true;

		// Use the callback to determine showing the tab, if it exists.
		if ( is_callable( $this->show_on_cb ) ) {
			$show = call_user_func( $this->show_on_cb, $this );
		}

		return $show;
	}

	/**
	 * Checks required user capabilities and whether the theme has the
	 * feature support required by the tab.
	 *
	 * @return bool False if theme doesn't support the tab or the user doesn't have the capability.
	 */
	public function check_capabilities() {
		if ( $this->capability && ! call_user_func_array( 'current_user_can', (array) $this->capability ) ) {
			return false;
		}

		if ( $this->theme_supports && ! call_user_func_array( 'current_theme_supports', (array) $this->theme_supports ) ) {
			return false;
		}

		if ( ! $this->should_show() ) {
			return false;
		}

		return true;
	}

	/**
	 * Get tab icon.
	 *
	 * @return string
	 */
	public function build_icon() {
		$icon_html = '';

		if ( empty( $this->icon ) ) {
			return '';
		}

		if ( 0 === strpos( $this->icon, 'dashicons-' ) ) {
			$icon_html = '<span class="cmb2-tab-icon cmb2-tab-dashicons dashicons ' . esc_attr( $this->icon ) . '"></span>';
		} elseif ( preg_match( '/^(http.*\.)(jpe?g|png|[tg]iff?|svg)$/', $this->icon ) ) {
			$icon_html = '<span class="cmb2-tab-icon cmb2-tab-imgicon" style="background-image:url(\'' . esc_attr( $this->icon ) . '\'"></span>';
		} else {
			$icon_html = '<span class="cmb2-tab-icon cmb2-tab-fonticons ' . esc_attr( $this->icon ) . '"></span>';
		}

		return $icon_html;
	}
}
