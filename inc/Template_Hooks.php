<?php
namespace AweBooking;

use AweBooking\Support\Service_Hooks;

class Template_Hooks extends Service_Hooks {
	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 *
	 * @param AweBooking $awebooking AweBooking Container instance.
	 */
	public function register( $awebooking ) {
		add_action( 'after_setup_theme', array( $this, 'add_image_sizes' ) );
	}

	/**
	 * Init service provider.
	 *
	 * This method will be run after container booted.
	 *
	 * @param AweBooking $awebooking AweBooking Container instance.
	 */
	public function init( $awebooking ) {
		add_filter( 'template_include', array( $this, 'template_loader' ), 10 );
		add_filter( 'body_class', array( $this, 'awebooking_body_class' ) );
	}

	/**
	 * Load a template.
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
			$template = awebooking_locate_template( $default_template );
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
	 * Add AweBooking Image sizes to WP.
	 */
	public function add_image_sizes() {
		$awebooking_thumbnail = awebooking_get_image_size( 'awebooking_thumbnail' );
		$awebooking_catalog	= awebooking_get_image_size( 'awebooking_catalog' );
		$awebooking_single	= awebooking_get_image_size( 'awebooking_single' );

		add_image_size( 'awebooking_thumbnail', $awebooking_thumbnail['width'], $awebooking_thumbnail['height'], $awebooking_thumbnail['crop'] );
		add_image_size( 'awebooking_catalog', $awebooking_catalog['width'], $awebooking_catalog['height'], $awebooking_catalog['crop'] );
		add_image_size( 'awebooking_single', $awebooking_single['width'], $awebooking_single['height'], $awebooking_single['crop'] );
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
