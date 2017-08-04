<?php
namespace AweBooking\Admin\Pages;

use CMB2_hookup;
use Skeleton\CMB2\CMB2;

use AweBooking\Room;
use AweBooking\Booking;
use AweBooking\Booking_Room_Item;
use AweBooking\Booking_Service_Item;
use AweBooking\AweBooking;
use AweBooking\Service;

use AweBooking\Support\Date_Utils;
use AweBooking\Support\Date_Period;
use AweBooking\BAT\Booking_Request;

use AweBooking\Admin\Forms\Service_Form;
use AweBooking\Admin\Forms\Add_Booking_Form;

class Add_Booking_Item {
	/**
	 * The admin page ID.
	 *
	 * @var string
	 */
	protected $page = 'awebooking-add-item';

	protected $booking;

	protected $form;
	protected $form_service;

	/**
	 * Add booking item constructor.
	 */
	public function __construct( Add_Booking_Form $form ) {
		$this->form = $form;
		$this->form_service = new Service_Form;
	}

	/**
	 * Init page hooks.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'register_page' ) );
	}

	/**
	 * Adds submenu page if there are plugin actions to take.
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_submenu_page
	 *
	 * @access private
	 */
	public function register_page() {
		$page_hook = add_submenu_page( null, '', '', 'manage_options', $this->page, [ $this, 'output' ] );

		add_action( 'load-' . $page_hook, function() {
			$_GET['noheader'] = true;
		});

		// Include CMB CSS in the head to avoid FOUC.
		add_action( "admin_print_styles-{$page_hook}", [ $this->form, 'enqueue_scripts' ] );
	}

	/**
	 * Setup the request data.
	 *
	 * @return void
	 */
	protected function setup_request_data() {
		$this->set_fields_visibility( false );

		// First, we need fill some data if available from request.
		if ( isset( $_REQUEST['check_in'] ) && isset( $_REQUEST['check_out'] ) ) {
			$check_in = sanitize_text_field( wp_unslash( $_REQUEST['check_in'] ) );
			$check_out = sanitize_text_field( wp_unslash( $_REQUEST['check_out'] ) );

			try {
				$date_period = new Date_Period( $check_in, $check_out, false );
				$this->form->get_field( 'check_in_out' )->set_prop( 'default', [ $check_in, $check_out ] );
			} catch ( \Exception $e ) {
				$this->add_validation_error( 'check_in_out', $e->getMessage() );
				return;
			}

			if ( $date_period->nights() <= 0 ) {
				return;
			}

			if ( isset( $_REQUEST['add_room'] ) ) {
				$this->form->get_field( 'add_room' )->set_prop( 'default',
					sanitize_text_field( wp_unslash( $_REQUEST['add_room'] ) )
				);
			} else {
				return;
			}

			// Call to Concierge and check availability our hotel.
			$concierge = awebooking()->make( 'concierge' );

			$results = $concierge->check_availability(
				new Booking_Request( $date_period )
			);

			$this->form->get_field( 'add_room' )->set_prop( 'options',
				$this->generate_select_rooms( $results )
			);

			$the_room = new Room( $this->form->get_value( 'add_room' ) );

			if ( $the_room->exists() ) {
				$room_type = $the_room->get_room_type();

				$max_adults = $room_type->get_number_adults() + $room_type->get_max_adults();
				$max_children = $room_type->get_number_children() + $room_type->get_max_children();

				$a = range( 1, $max_adults );
				$b = range( 0, $max_children );

				$this->form->get_field( 'adults' )->set_prop( 'options', array_combine( $a, $a ) );
				$this->form->get_field( 'children' )->set_prop( 'options', array_combine( $b, $b ) );
				$this->form->get_field( 'price' )->set_prop( 'default', $room_type->get_base_price()->get_amount() );

				$this->set_fields_visibility( true );

				// Setup extra services form.
				$services = [];
				foreach ( $room_type->get_services() as $service ) {
					$services[ $service->get_id() ] = $service->get_describe();
				}
				$this->form_service->get_field( 'extra_services' )->set_prop( 'options', $services );
			}
		} // End if().
	}

	protected function set_fields_visibility( $show = true ) {
		$callback = $show ? '__return_true' : '__return_false';

		$this->form->get_field( 'price' )->set_prop( 'show_on_cb', $callback );
		$this->form->get_field( 'adults' )->set_prop( 'show_on_cb', $callback );
		$this->form->get_field( 'children' )->set_prop( 'show_on_cb', $callback );
	}

	/**
	 * //
	 *
	 * @param  array $results //.
	 * @return array
	 */
	protected function generate_select_rooms( array $results ) {
		$options = [];

		foreach ( $results as $availability ) {
			if ( $availability->unavailable() ) {
				continue;
			}

			$room_type = $availability->get_room_type();

			foreach ( $availability->get_rooms() as $room ) {
				$options[ $room->get_id() ] = $room_type['title'] . ' (' . $room['name'] . ')';
			}
		}

		return $options;
	}

	protected function prepare_setup() {
		$error_message = esc_html__( 'Sorry, you are not allowed to access this page.', 'awebooking' );

		if ( empty( $_REQUEST['booking'] ) ) {
			wp_die( $error_message, 403 );
		}

		$the_booking = new Booking( absint( $_REQUEST['booking'] ) );
		if ( ! $the_booking->exists() ) {
			wp_die( $error_message, 403 );
		}

		$this->booking = $the_booking;
	}

	protected function handle_form() {
		if ( isset( $_POST[ $this->form->nonce() ] ) && wp_verify_nonce( $_POST[ $this->form->nonce() ], $this->form->nonce() ) ) {
			$input_data = $this->form->get_sanitized_values( $_POST );
			$service_data = $this->form_service->get_sanitized_values( $_POST );

			$room = new Room( $input_data['add_room'] );
			$room_type = $room->get_room_type();

			$concierge = awebooking()->make( 'concierge' );

			try {
				$date_period = new Date_Period( $input_data['check_in_out'][0], $input_data['check_in_out'][1], false );
			} catch ( \Exception $e ) {
				return;
			}

			$request = new Booking_Request( $date_period, [
				'adults'   => $input_data['adults'],
				'children' => $input_data['children'],
			]);

			$availability = $concierge->check_room_type_availability(
				$room->get_room_type(), $request
			);

			if ( $availability->available() && in_array( $room->get_id(), $availability->get_rooms_ids() ) ) {
				$item = new Booking_Room_Item;

				$item['name'] = $room_type->get_title();
				$item['check_in'] = $date_period->get_start_date()->toDateString();
				$item['check_out'] = $date_period->get_end_date()->toDateString();
				$item['adults'] = $input_data['adults'];
				$item['children'] = $input_data['children'];
				$item['room_id'] = $room->get_id();
				$item['total'] = $input_data['price'];
				$item['subtotal'] = $input_data['price'];
				$this->booking->add_item( $item );

				// handler services.
				$service_data = array_map(function($s) {
					return new Service( $s );
				}, $service_data['extra_services']);

				foreach ( $service_data as $service ) {
					if ( ! $service->exists() ) {
						continue;
					}

					// Add service item into booking.
					$service_item = new Booking_Service_Item;
					$service_item['name'] = $service->get_name();
					// $service_item['room_id'] = $room->get_id();
					$service_item['service_id'] = $service->get_id();
					$service_item['price'] = $service->get_price()->get_amount();

					$this->booking->add_item( $service_item );
				}

				$this->booking->save();
			}

			wp_safe_redirect(
				get_edit_post_link( $this->booking->get_id(), 'link' )
			);
		} // End if().
	}

	/**
	 * Admin page markup. Mostly handled by CMB2.
	 *
	 * @access private
	 */
	public function output() {
		$this->prepare_setup();

		$this->setup_request_data();

		$this->handle_form();

		require_once ABSPATH . 'wp-admin/admin-header.php';

		?><div class="wrap cmb2-options-page">
			<h1 class="wp-heading-inline" style="margin-bottom: 15px;">Add room to booking #</h1>
			<hr class="wp-header-end">

			<style type="text/css">
				.cmb2-options-page .cmb2-wrap,
				.cmb2-options-page .cmb2-wrap select {
					width: 100%;
				}
			</style>

			<form method="POST" action="">
				<input type="hidden" name="page" value="<?php echo esc_attr( $this->page ); ?>">

				<div class="" style="width: 475px; float: left; margin-right: 15px;">
					<?php $this->form->output(); ?>

					<div class="clear"></div>
					<br>

					<a href="<?php echo esc_url( get_edit_post_link( $this->booking->get_id() ) ); ?>" class="button button-primary"><?php echo esc_html__( 'Cancel', 'awebooking' ) ?></a>
					<input class="button" type="submit" name="add_room_submit" value="Add Room" style="float: right">
				</div>

				<div class="" style="width: 475px; float: left; margin-right: 15px;">
					<?php $this->form_service->output(); ?>
				</div>
			</form>

			<script type="text/javascript">
			(function($) {
				'use strict';

				var updateQueryStringParam = function (key, value) {
					var baseUrl = [location.protocol, '//', location.host, location.pathname].join(''),
						urlQueryString = document.location.search,
						newParam = key + '=' + value,
						params = '?' + newParam;

					// If the "search" string exists, then build params from it
					if (urlQueryString) {
						var keyRegex = new RegExp('([\?&])' + key + '[^&]*');

						// If param exists already, update it
						if (urlQueryString.match(keyRegex) !== null) {
							params = urlQueryString.replace(keyRegex, "$1" + newParam);
						} else { // Otherwise, add it to end of query string
							params = urlQueryString + '&' + newParam;
						}
					}
					window.history.replaceState({}, "", baseUrl + params);
				};

				$(function($) {
					var $el = $('#cmb2-metabox-add_booking_form');

					var updateFirst = function() {
						var $check_in  = $el.find('#check_in_out_0');
						var $check_out = $el.find('#check_in_out_1');

						if (! $check_in.val() || ! $check_out.val()) {
							return;
						}

						updateQueryStringParam('check_in', $check_in.val());
						updateQueryStringParam('check_out', $check_out.val());
						updateQueryStringParam('add_room', '');

						window.location.reload();
					};

					var updateSecond = function() {
						var $add_room = $el.find('#add_room');

						updateQueryStringParam('add_room', $add_room.val());
						window.location.reload();
					};

					$el.find('#check_in_out_0').on('change', updateFirst);
					$el.find('#check_in_out_1').on('change', updateFirst);
					$el.find('#add_room').on('change', updateSecond);
				});
			})(jQuery);
			</script>

		</div><?php
	}
}
