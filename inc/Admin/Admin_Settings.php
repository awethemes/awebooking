<?php
namespace AweBooking\Admin;

use Skeleton\Menu_Page;
use Skeleton\Admin_Page;
use AweBooking\AweBooking;

class Admin_Settings extends Admin_Page {
	/**
	 * //
	 *
	 * @var Config
	 */
	protected $config;

	/**
	 * Make a new page settings.
	 */
	public function __construct() {
		$this->config = awebooking( 'config' );

		$this->strings = array(
			'updated' => esc_html__( 'Your settings have been saved.', 'awebooking' ),
		);

		parent::__construct( awebooking( 'option_key' ) );

		$this->set(array(
			// A page-id should only use alpha-dash styles.
			'menu_slug'  => 'awebooking-settings',
			// 'page_title' => esc_html__( 'Settings', 'awebooking' ),
			'menu_title' => esc_html__( 'Settings', 'awebooking' ),
		));

		// Register the settings.
		$this->register_general_settings();
		$this->register_backups();

		do_action( 'awebooking/admin_settings/register', $this );
	}

	/**
	 * Init page hooks.
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		awebooking( 'admin_menu' )->add_submenu( $this->menu_slug, array(
			'page_title'  => $this->page_title,
			'menu_title'  => $this->menu_title,
			'function'    => $this->render_callback,
		));

		// Hook in our save notices.
		add_action( "cmb2_save_options-page_fields_{$this->prop( 'id' )}", array( $this, 'settings_notices' ), 10, 3 );

		return $this;
	}

	/**
	 * Register general section settings.
	 *
	 * @return void
	 */
	public function register_general_settings() {
		$section = $this->add_section( 'general', [
			'title' => esc_html__( 'General', 'awebooking' ),
			'priority' => 10,
		]);

		$section->add_field( array(
			'id'   => '__general_system__',
			'type' => 'title',
			'name' => esc_html__( 'AweBooking General', 'awebooking' ),
			'priority' => 10,
		) );

		$section->add_field( array(
			'id'       => 'enable_location',
			'type'     => 'toggle',
			'name'     => esc_html__( 'Multi hotel location?', 'awebooking' ),
			'default'  => $this->config->get_default( 'enable_location' ),
			'priority' => 10,
		) );

		$section->add_field( array(
			'id'       => 'location_default',
			'type'     => 'select',
			'name'     => esc_html__( 'Default location', 'awebooking' ),
			'description' => esc_html__( 'Select a default location.', 'awebooking' ),
			'options_cb'  => wp_data_callback( 'terms',  array(
				'taxonomy'   => AweBooking::HOTEL_LOCATION,
				'hide_empty' => false,
			)),
			'validate' => 'integer',
			'deps'     => array( 'enable_location', '==', true ),
			'priority' => 15,
		) );

		$section->add_field( array(
			'id'       => 'date_format',
			'type'     => 'text_small',
			'name'     => esc_html__( 'Date format', 'awebooking' ),
			'default'  => $this->config->get_default( 'date_format' ),
			'render_field_cb'   => array( $this, '_date_format_field_callback' ),
			'priority' => 20,
		) );

		$section->add_field( array(
			'id'   => '__general_currency__',
			'type' => 'title',
			'name' => esc_html__( 'Currency Options', 'awebooking' ),
			'priority' => 25,
		) );

		$section->add_field( array(
			'id'       => 'currency',
			'type'     => 'select',
			'name'     => esc_html__( 'Currency', 'awebooking' ),
			'default' => $this->config->get_default( 'currency' ),
			'options'  => awebooking( 'currency_manager' )->get_for_dropdown( '%name (%symbol)' ),
			'priority' => 25,
		) );

		$section->add_field( array(
			'id'       => 'currency_position',
			'type'     => 'select',
			'name'     => esc_html__( 'Currency position', 'awebooking' ),
			// 'desc'     => esc_html__( 'Controls the position of the currency symbol.', 'awebooking' ),
			'default'  => $this->config->get_default( 'currency_position' ),
			'validate' => 'required',
			'options'  => awebooking( 'config' )->get_currency_positions(),
			'priority' => 30,
		) );

		$section->add_field( array(
			'type'     => 'text_small',
			'id'       => 'price_thousand_separator',
			'name'     => esc_html__( 'Thousand separator', 'awebooking' ),
			// 'desc'     => esc_html__( 'Sets the thousand separator of displayed prices.', 'awebooking' ),
			'default'  => $this->config->get_default( 'price_thousand_separator' ),
			'validate' => 'required',
			'priority' => 35,
		) );

		$section->add_field( array(
			'type'     => 'text_small',
			'id'       => 'price_decimal_separator',
			'name'     => esc_html__( 'Decimal separator', 'awebooking' ),
			// 'desc'     => esc_html__( 'Sets the decimal separator of displayed prices.', 'awebooking' ),
			'default'  => $this->config->get_default( 'price_decimal_separator' ),
			'validate' => 'required',
			'priority' => 40,
		) );

		$section->add_field( array(
			'type'       => 'text_small',
			'id'         => 'price_number_decimals',
			'name'       => esc_html__( 'Number of decimals', 'awebooking' ),
			'default'    => $this->config->get_default( 'price_number_decimals' ),
			'validate'   => 'required|integer|min:0',
			'attributes' => array(
				'min'  => 0,
				'type' => 'number',
			),
			'priority' => 45,
		) );

		// Display.
		$display = $this->add_section( 'display', [
			'title' => esc_html__( 'Display', 'awebooking' ),
			'priority' => 20,
		]);

		$display->add_field( array(
			'id'   => '__display_pages__',
			'type' => 'title',
			'name' => esc_html__( 'AweBooking Pages', 'awebooking' ),
			'description' => esc_html__( 'These pages need to be set so that AweBooking knows where to send users to handle.', 'awebooking' ),
			'priority' => 10,
		) );

		$display->add_field( array(
			'id'           => 'page_check_availability',
			'type'         => 'select',
			'name'         => esc_html__( 'Check Availability', 'awebooking' ),
			'description'  => esc_html__( 'Selected page to display check availability form.', 'awebooking' ),
			'default'      => $this->config->get_default( 'page_check_availability' ),
			'options_cb'   => wp_data_callback( 'pages', array( 'post_status' => 'publish' ) ),
			'validate'     => 'integer',
			'priority'     => 10,
			'show_option_none' => true,
		) );

		$display->add_field( array(
			'id'          => 'page_booking',
			'type'        => 'select',
			'name'        => esc_html__( 'Booking Informations', 'awebooking' ),
			'description' => esc_html__( 'Selected page to display booking informations.', 'awebooking' ),
			'default'     => $this->config->get_default( 'page_booking' ),
			'options_cb'  => wp_data_callback( 'pages', array( 'post_status' => 'publish' ) ),
			'validate'    => 'integer',
			'priority'    => 15,
			'show_option_none' => true,
		) );

		$display->add_field( array(
			'id'          => 'page_checkout',
			'type'        => 'select',
			'name'        => esc_html__( 'Confirm Booking', 'awebooking' ),
			'description' => esc_html__( 'Selected page to display checkout informations.', 'awebooking' ),
			'default'     => $this->config->get_default( 'page_checkout' ),
			'options_cb'  => wp_data_callback( 'pages', array( 'post_status' => 'publish' ) ),
			'validate'    => 'integer',
			'priority'    => 20,
			'show_option_none' => true,
		) );

		$display->add_field( array(
			'id'       => '__display_check_availability__',
			'type'     => 'title',
			'name'     => esc_html__( 'Check availability', 'awebooking' ),
			'priority' => 25,
		) );

		$display->add_field( array(
			'id'         => 'check_availability_max_adults',
			'type'       => 'text_small',
			'attributes' => array( 'type' => 'number' ),
			'name'       => esc_html__( 'Max adults', 'awebooking' ),
			'default'    => $this->config->get_default( 'check_availability_max_adults' ),
			'validate'   => 'integer|min:1',
			'priority'   => 30,
			'sanitization_cb' => 'absint',
		) );

		$display->add_field( array(
			'id'         => 'check_availability_max_children',
			'type'       => 'text_small',
			'attributes' => array( 'type' => 'number' ),
			'name'       => esc_html__( 'Max children', 'awebooking' ),
			'default'    => $this->config->get_default( 'check_availability_max_children' ),
			'validate'   => 'integer|min:0',
			'priority'   => 35,
			'sanitization_cb' => 'absint',
		) );

		$display->add_field( array(
			'id'   => 'page_for_check_availability',
			'type' => 'title',
			'name' => esc_html__( 'Page for check availability ', 'awebooking' ),
			'priority' => 40,
		) );

		$display->add_field( array(
			'id'       => 'showing_price',
			'type'     => 'select',
			'name'     => esc_html__( 'Showing price', 'awebooking' ),
			'description' => esc_html__( 'Selected a type of price to showing in the checking availability page.', 'awebooking' ),
			'default'  => $this->config->get_default( 'showing_price' ),
			'options'	 => array(
				'start_price'	 => esc_html__( 'Starting price', 'awebooking' ),
				'average_price'  => esc_html__( 'Average price', 'awebooking' ),
				'total_price' 	 => esc_html__( 'Total price', 'awebooking' ),
			),
			'show_option_none' => false,
			'priority' => 45,
		) );

		// Email.
		$email = $this->add_section( 'email', [
			'title'    => esc_html__( 'Email', 'awebooking' ),
			'priority' => 30,
		]);

		$email->add_field([
			'id'   => '__email_sender__',
			'type' => 'title',
			'name' => esc_html__( 'Email sender', 'awebooking' ),
		]);

		$email->add_field([
			'id'       => 'email_from_name',
			'type'     => 'text',
			'name'     => esc_html__( '"From" name', 'awebooking' ),
			'default'  => $this->config->get_default( 'email_from_name' ),
		]);

		$email->add_field([
			'id'       => 'email_from_address',
			'type'     => 'text',
			'name'     => esc_html__( '"From" address', 'awebooking' ),
			'default'  => $this->config->get_default( 'email_from_address' ),
			'validate' => 'email',
		]);

		// ...
		$email->add_field( array(
			'id'   => '__email__',
			'type' => 'title',
			'name' => esc_html__( 'Email Settings', 'awebooking' ),
		) );

		$email->add_field( array(
			'name'    => esc_html__( 'Base Color', 'awebooking' ),
			'id'      => 'email_base_color',
			'type'    => 'colorpicker',
			'default' => $this->config->get_default( 'email_base_color' ),
		) );

		$email->add_field( array(
			'name'    => esc_html__( 'Body Background Color', 'awebooking' ),
			'id'      => 'email_bg_color',
			'type'    => 'colorpicker',
			'default' => $this->config->get_default( 'email_bg_color' ),
		) );


		$email->add_field( array(
			'name'    => esc_html__( 'Email Background Color', 'awebooking' ),
			'id'      => 'email_body_bg_color',
			'type'    => 'colorpicker',
			'default' => $this->config->get_default( 'email_body_bg_color' ),
		) );

		$email->add_field( array(
			'name'    => esc_html__( 'Email Body Text Color', 'awebooking' ),
			'id'      => 'email_body_text_color',
			'type'    => 'colorpicker',
			'default' => $this->config->get_default( 'email_body_text_color' ),
		) );

		$email->add_field( array(
			'name'    => esc_html__( 'Email Copyright', 'awebooking' ),
			'id'      => 'email_copyright',
			'type'    => 'text',
			'default' => $this->config->get_default( 'email_copyright' ),
		) );

		$email->add_field( array(
			'name'    => esc_html__( 'Admin notify', 'awebooking' ),
			'id'      => 'email_admin_notify',
			'type'    => 'toggle',
			'desc'    => esc_html__( 'Send to email address from General Settings', 'awebooking' ),
			'default' => $this->config->get_default( 'email_admin_notify' ),
		) );

		$email->add_field( array(
			'name'    => esc_html__( 'Notify another email address', 'awebooking' ),
			'id'      => 'email_notify_another_emails',
			'type'    => 'text',
			'desc'    => esc_html__( 'Enter some emails by "," separating values.', 'awebooking' ),
		) );

		$email->add_field( array(
			'id'   => '__email_new_booking__',
			'type' => 'title',
			'description' => __( 'Email settings for new booking. Click <a href="?page=awebooking-email-preview&status=new" target="_blank">here</a> to preview.', 'awebooking' ),
			'name' => esc_html__( 'New booking','awebooking' ),
		) );

		$email->add_field( array(
			'name'    => esc_html__( 'Enable?', 'awebooking' ),
			'id'      => 'email_new_enable',
			'type'    => 'toggle',
			'desc'    => esc_html__( 'Check to turn on email notification for new booking', 'awebooking' ),
			'default' => $this->config->get_default( 'email_new_enable' ),
		) );

		$email->add_field( array(
			'name'    => esc_html__( 'Email subject', 'awebooking' ),
			'id'      => 'email_new_subject',
			'type'    => 'textarea_small',
			'default' => '[{site_title}] New customer booking #{order_number} - {order_date}',
			'desc'    => esc_html__( 'This controls the email subject line. Leave blank to use the default subject', 'awebooking' ). ': [{site_title}] New customer booking ({order_number}) - {order_date}',
			'attributes' => array( 'style' => 'height:50px;' ),
		) );

		$email->add_field( array(
			'name'    => esc_html__( 'Email header', 'awebooking' ),
			'id'      => 'email_new_header',
			'type'    => 'text',
			'default' => esc_html__( 'New customer booking', 'awebooking' ),
			'desc'    => esc_html__( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: Your booking is completed', 'awebooking' ),
		) );

		$email->add_field( array(
			'id'   => '__email_cancelled_booking__',
			'type' => 'title',
			'description' => __( 'Email settings for cancelled booking. Click <a href="?page=awebooking-email-preview&status=cancelled" target="_blank">here</a> to preview.', 'awebooking' ),
			'name' => esc_html__( 'Cancelled booking','awebooking' ),
		) );

		$email->add_field( array(
			'name'    => esc_html__( 'Enable?', 'awebooking' ),
			'id'      => 'email_cancelled_enable',
			'type'    => 'toggle',
			'desc'    => esc_html__( 'Check to turn on email notification for cancelled booking', 'awebooking' ),
			'default' => $this->config->get_default( 'email_cancelled_enable' ),
		) );

		$email->add_field( array(
			'name'    => esc_html__( 'Email subject', 'awebooking' ),
			'id'      => 'email_cancelled_subject',
			'type'    => 'textarea_small',
			'default' => 'Your {site_title} booking receipt from {order_date}',
			'desc'    => esc_html__( 'This controls the email subject line. Leave blank to use the default subject', 'awebooking' ). ': Your {site_title} booking receipt from {order_date}',
			'attributes' => array('style' => 'height:50px;' ),
		) );


		$email->add_field( array(
			'name'    => esc_html__( 'Email header', 'awebooking' ),
			'id'      => 'email_cancelled_header',
			'type'    => 'text',
			'default' => esc_html__( 'Your booking is cancelled', 'awebooking' ),
			'desc'    => esc_html__( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: Thank you for your booking.', 'awebooking' ),
		) );

		$email->add_field( array(
			'id'   => '__email_completed_booking__',
			'type' => 'title',
			'name' => esc_html__( 'Completed booking','awebooking' ),
			'description' => __( 'Email settings for completed booking. Click <a href="?page=awebooking-email-preview&status=completed" target="_blank">here</a> to preview.', 'awebooking' ),
		) );

		$email->add_field( array(
			'name'    => esc_html__( 'Enable', 'awebooking' ),
			'id'      => 'email_complete_enable',
			'type'    => 'toggle',
			'desc'    => esc_html__( 'Check to turn on email notification for complete booking', 'awebooking' ),
			'default' => $this->config->get_default( 'email_complete_enable' ),
		) );

		$email->add_field( array(
			'name'    => esc_html__( 'Email subject', 'awebooking' ),
			'id'      => 'email_complete_subject',
			'type'    => 'textarea_small',
			'default' => 'Your {site_title} booking from {order_date} is complete',
			'desc'    => esc_html__( 'This controls the email subject line. Leave blank to use the default subject', 'awebooking' ) . ': Your {site_title} booking from {order_date} is complete',
			'attributes' => array( 'style' => 'height:50px;' ),
		) );

		$email->add_field( array(
			'name'    => esc_html__( 'Email header', 'awebooking' ),
			'id'      => 'email_complete_header',
			'type'    => 'text',
			'default' => esc_html__( 'Your booking is completed', 'awebooking' ),
			'desc'    => esc_html__( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: Your booking is completed.', 'awebooking' ),
		) );
	}

	/**
	 * Register backup and restore.
	 *
	 * @return void
	 */
	public function register_backups() {
		$backup_section = $this->add_section( 'backup', [
			'title' => esc_html__( 'Backups', 'awebooking' ),
			'priority' => 60,
		]);

		$backup_section->add_field([
			'id'   => 'backups',
			'type' => 'backups',
		]);
	}

	public function _date_format_field_callback( $field_args, $field ) {

		$date_formats = array_unique( apply_filters( 'awebooking/date_formats', array( __( 'F j, Y' ), 'Y-m-d', 'm/d/Y', 'd/m/Y' ) ) );

		$custom = true;

		foreach ( $date_formats as $format ) {
			echo "\t<label><input type='radio' name='date_format_default' value='" . esc_attr( $format ) . "'";
			if ( awebooking_option( 'date_format' ) === $format ) { // checked() uses "==" rather than "==="
				echo " checked='checked'";
				$custom = false;
			}
			echo ' /> <span class="cmb2-date-time-text format-i18n">' . date_i18n( $format ) . '</span><code>' . esc_html( $format ) . "</code></label><br />\n";
		}

		echo '<label><input type="radio" name="date_format_default" id="date_format_radio" value="\c\u\s\t\o\m"';
		checked( $custom );
		echo '/> <span class="cmb2-date-time-text date-time-custom-text">' . __( 'Custom:' ) . '<span class="screen-reader-text"> ' . __( 'enter a custom date format in the following field' ) . '</span></label>' .
			'<label for="date_format" class="screen-reader-text">' . __( 'Custom date format:' ) . '</label>';

		skeleton_render_field( $field );

		echo '</span>' .
		'<span class="screen-reader-text">' . __( 'example:' ) . ' </span> <span class="example">' . date_i18n( awebooking_option( 'date_format' ) ) . '</span>' .
		"<span class='spinner'></span>\n";

			?>
			<script type="text/javascript">
				jQuery(function($) {
					$("input[name='date_format_default']").click(function(){
						if ( "date_format_radio" != $(this).attr("id") )
							$( "input[name='date_format']" ).val( $( this ).val() ).siblings( '.example' ).text( $( this ).parent( 'label' ).children( '.format-i18n' ).text() );
					});
					$("input[name='date_format']").focus(function(){
						$( '#date_format_radio' ).prop( 'checked', true );
					});
				})
			</script>

		<?php
	}
}
