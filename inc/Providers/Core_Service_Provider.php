<?php
namespace AweBooking\Providers;

use AweBooking\Setting;
use AweBooking\Cart\Cart;
use AweBooking\Booking\Store;
use AweBooking\Money\Currency;
use AweBooking\Money\Currencies;
use AweBooking\Shortcodes\Shortcodes;
use AweBooking\Support\Service_Provider;

class Core_Service_Provider extends Service_Provider {
	/**
	 * The AweBooking core widgets.
	 *
	 * @var array
	 */
	protected $widgets = [
		\AweBooking\Widgets\Check_Availability_Widget::class,
	];

	/**
	 * The AweBooking core shortcodes.
	 *
	 * @var array
	 */
	protected $shortcodes = [
		'awebooking_check_form'         => \AweBooking\Shortcodes\Check_Form_Shortcode::class,
		'awebooking_check_availability' => \AweBooking\Shortcodes\Check_Availability_Shortcode::class,
		'awebooking_checkout'           => \AweBooking\Shortcodes\Checkout_Shortcode::class,
		'awebooking_room_types'         => \AweBooking\Shortcodes\Room_Types_Shortcode::class,
	];

	/**
	 * Registers services on the AweBooking.
	 */
	public function register() {
		$this->awebooking->singleton( 'currencies', function() {
			return new Currencies;
		});

		$this->awebooking->alias( 'currencies', 'currency_manager' );

		$this->awebooking->singleton( 'currency', function( $a ) {
			return $a['currencies']->get( $a['setting']->get( 'currency' ) );
		});

		$this->register_widgets();
	}

	/**
	 * Init the core widgets.
	 *
	 * @return void
	 */
	protected function register_widgets() {
		add_action( 'widgets_init', function() {
			$widgets = apply_filters( 'awebooking/widgets', $this->widgets );

			// Loop each widgets and call the register.
			foreach ( $widgets as $class_name ) {
				register_widget( $class_name );
			}
		});
	}

	/**
	 * Init (boot) the service provider.
	 *
	 * @return void
	 */
	public function init() {
		$this->modify_setting_key();

		$this->init_shortcodes();
	}

	/**
	 * Init the core shortcodes.
	 *
	 * @return void
	 */
	protected function init_shortcodes() {
		$shortcodes = apply_filters( 'awebooking/shortcodes', $this->shortcodes );

		foreach ( $shortcodes as $tag => $class_name ) {
			add_shortcode( $tag, function( $atts, $contents = '' ) use ( $class_name ) {
				return $this->awebooking
					->makeWith( $class_name, compact( 'atts', 'contents' ) )
					->build();
			});
		}
	}

	/**
	 * Modify the setting key when running on multilanguage.
	 *
	 * @return string
	 */
	protected function modify_setting_key() {
		if ( ! $this->awebooking->is_running_multilanguage() ) {
			return;
		}

		$current_setting = $this->awebooking['setting_key'];
		$active_language = $this->awebooking['multilingual']->get_active_language();

		// If active language is not "en", "" or all, suffix with current language.
		if ( ! in_array( $active_language, [ '', 'en', 'all' ] ) ) {
			$new_setting = $current_setting . '_' . $active_language;

			$this->perform_copy_settings( $current_setting, $new_setting );

			$this->awebooking->instance( 'setting_key', $new_setting );

			if ( $this->awebooking->resolved( 'setting' ) ) {
				$this->awebooking->instance( 'setting', new Setting( $new_setting ) );
			}
		}
	}

	/**
	 * Perform the copy settings.
	 *
	 * @param  string $current_setting The current setting key.
	 * @param  string $new_setting     The new setting key.
	 * @return void
	 */
	protected function perform_copy_settings( $current_setting, $new_setting ) {
		$new_options     = (array) get_option( $new_setting, [] );
		$current_options = (array) get_option( $current_setting, [] );

		if ( empty( $new_options ) && $current_options ) {
			update_option( $new_setting, $current_options );
		}
	}
}
