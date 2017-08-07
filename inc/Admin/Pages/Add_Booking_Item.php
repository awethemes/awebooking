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
	protected $services = [];

	/**
	 * Add booking item constructor.
	 */
	public function __construct( Add_Booking_Form $form ) {
		$this->form = $form;
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

			$the_room = new Room( $this->form['add_room']->get_value() );

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
				$this->services = $room_type->get_services();
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

	/**
	 * Admin page markup. Mostly handled by CMB2.
	 *
	 * @access private
	 */
	public function output() {
		$this->prepare_setup();

		$this->setup_request_data();

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

			<form method="POST" action="<?php echo esc_url( admin_url( 'post.php?post=' . $this->booking->get_id() . '&action=add_awebooking_room_item' ) ); ?>">
				<input type="hidden" name="page" value="<?php echo esc_attr( $this->page ); ?>">

				<div class="" style="width: 475px; float: left; margin-right: 15px;">
					<?php $this->form->output(); ?>

					<div class="clear"></div>
					<br>

					<a href="<?php echo esc_url( get_edit_post_link( $this->booking->get_id() ) ); ?>" class="button button-primary"><?php echo esc_html__( 'Cancel', 'awebooking' ) ?></a>
					<input class="button" type="submit" name="add_room_submit" value="Add Room" style="float: right">
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
