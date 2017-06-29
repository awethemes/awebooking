<?php
namespace AweBooking;

use Skeleton\Container\Service_Hooks;

class Template_Hooks extends Service_Hooks {
	/**
	 * Init service provider.
	 *
	 * This method will be run after container booted.
	 *
	 * @param AweBooking $awebooking AweBooking Container instance.
	 */
	public function init( $awebooking ) {
		add_filter( 'template_include', array( $this, 'template_loader' ), 10 );
		add_action( 'after_setup_theme', array( $this, 'add_image_sizes' ) );
		add_filter( 'display_post_states', array( $this, 'page_state' ), 10, 2 );
		add_filter( 'body_class', array( $this, 'awebooking_body_class' ) );
		// add_filter( 'template_include', array( $this, 'handle_check_availability_form' ), 5 );
	}

	/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the themes.
	 *
	 * Templates are in the 'templates' folder. AweBooking looks for theme
	 * overrides in /theme/awebooking/ by default.
	 *
	 * @param mixed $template //.
	 * @return string
	 */
	public function template_loader( $template ) {
		if ( is_embed() ) {
			return $template;
		}

		// Get default template filename by guest current context.
		$default_template = $this->find_default_template();

		if ( $default_template ) {
			// First, we'll try look template in current theme.
			$template = locate_template(
				$this->search_template_files( $default_template )
			);

			// Then, if not use own default templates.
			if ( ! $template ) {
				$template = awebooking()->plugin_path() . '/templates/' . $default_template;
			}
		}

		return $template;
	}

	/**
	 * Get the default filename for a template.
	 *
	 * @return string
	 */
	protected function find_default_template() {
		$template = '';

		switch ( true ) {
			case is_singular( 'room_type' ):
				$template = 'single-room-type.php';
				break;

			case is_post_type_archive( 'room_type' ):
				$template = 'archive-room-type.php';
				break;
		}

		return $template;
	}

	/**
	 * Get an array of filenames to search for a given template.
	 *
	 * @param  string $default_template The default file name.
	 * @return array
	 */
	protected function search_template_files( $default_template ) {
		$search_files   = apply_filters( 'awebooking/template/files', array(), $default_template );

		$search_files[] = $default_template;
		$search_files[] = awebooking()->template_path() . $default_template;

		return array_unique( $search_files );
	}

	/**
	 * Set default image sizes.
	 */
	public function set_default_image_sizes() {

		$awebooking_thumbnail = array(
			'width'  => '300',
			'height' => '300',
			'crop'   => 1,
		);

		$awebooking_catalog = array(
			'width'  => '600',
			'height' => '400',
			'crop'   => 1,
		);

		$awebooking_single = array(
			'width'  => '640',
			'height' => '640',
			'crop'   => 1,
		);

		add_option( 'awebooking_thumbnail_image_size', $awebooking_thumbnail );
		add_option( 'awebooking_catalog_image_size', $awebooking_catalog );
		add_option( 'awebooking_single_image_size', $awebooking_single );
	}

	/**
	 * Add Awebooking Image sizes to WP.
	 */
	public function add_image_sizes() {
		$awebooking_thumbnail = abkng_get_image_size( 'awebooking_thumbnail' );
		$awebooking_catalog	= abkng_get_image_size( 'awebooking_catalog' );
		$awebooking_single	= abkng_get_image_size( 'awebooking_single' );

		add_image_size( 'awebooking_thumbnail', $awebooking_thumbnail['width'], $awebooking_thumbnail['height'], $awebooking_thumbnail['crop'] );
		add_image_size( 'awebooking_catalog', $awebooking_catalog['width'], $awebooking_catalog['height'], $awebooking_catalog['crop'] );
		add_image_size( 'awebooking_single', $awebooking_single['width'], $awebooking_single['height'], $awebooking_single['crop'] );
	}

	/**
	 * Add state for check availability page. TODO: Move to admin page.
	 *
	 * @param  array $post_states post_states.
	 * @param  void  $post        post object.
	 *
	 * @return array
	 */
	public function page_state( $post_states, $post ) {
		if ( intval( abkng_config( 'page_check_availability' ) ) === $post->ID ) {
			$post_states['page_check_availability'] = __( 'Check Availability Page' );
		}

		if ( intval( abkng_config( 'page_booking' ) ) === $post->ID ) {
			$post_states['page_booking'] = __( 'Booking Informations Page' );
		}

		if ( intval( abkng_config( 'page_checkout' ) ) === $post->ID ) {
			$post_states['page_checkout'] = __( 'Checkout Page' );
		}

		return $post_states;
	}

	/**
	 * Add class to body tag with check availability page.
	 *
	 * @param  array $classes classes.
	 * @return array
	 */
	public function awebooking_body_class( $classes ) {

		if ( is_awebooking() ) {
			$classes[] = 'awebooking';
			$classes[] = 'awebooking-page';
		}

		if ( is_room_type_archive() ) {
			$classes[] = 'awebooking-room-type-archive';
		}

		if ( is_room_type() ) {
			$classes[] = 'awebooking-room-type';
		}

		if ( is_check_availability_page() ) {
			$classes[] = 'awebooking-check-availability-page';
		}

		if ( is_booking_info_page() ) {
			$classes[] = 'awebooking-booking-info-page';
		}

		if ( is_booking_checkout_page() ) {
			$classes[] = 'awebooking-checkout-page';
		}

		return $classes;
	}
}

