<?php
namespace AweBooking\Admin\Pages;

use CMB2_hookup;
use Skeleton\CMB2\CMB2;

use AweBooking\Room;
use AweBooking\Booking;
use AweBooking\Booking_Room_Item;
use AweBooking\AweBooking;

use AweBooking\Support\Date_Utils;
use AweBooking\Support\Date_Period;
use AweBooking\BAT\Booking_Request;

class Edit_Booking_Item extends CMB2 {
	/**
	 * The admin page ID.
	 *
	 * @var string
	 */
	protected $page = 'awebooking-edit-item';

	protected $booking;

	/**
	 * Add booking item constructor.
	 */
	public function __construct() {
		parent::__construct([
			'id'           => $this->page,
			'object_types' => 'options-page',
			'hookup'       => false,
			'cmb_styles'   => false,
			'show_on'      => [ 'options-page' => $this->page ],
		]);

		$this->object_id( $this->page );
		$this->object_type( 'options-page' );

		$this->register_fields();
	}

	/**
	 * Init page hooks.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, '_register_page' ) );
	}

	/**
	 * Register fields in to the CMB2.
	 *
	 * @return void
	 */
	protected function register_fields() {
		$this->add_field( array(
			'id'          => 'check_in_out',
			'type'        => 'date_range',
			'name'        => esc_html__( 'Check-in/out', 'awebooking' ),
			'validate'    => 'required',
			'attributes'  => [ 'placeholder' => AweBooking::DATE_FORMAT, 'required' => true ],
			'date_format' => AweBooking::DATE_FORMAT,
		));

		$this->add_field( array(
			'id'          => 'add_room',
			'type'        => 'select',
			'name'        => esc_html__( 'Room', 'awebooking' ),
			'validate'    => 'required|integer|min:1',
			'sanitization_cb'  => 'absint',
			'show_option_none' => esc_html__( 'Choose a room...', 'awebooking' ),
		));

		$this->add_field( array(
			'id'               => 'adults',
			'type'             => 'select',
			'name'             => esc_html__( 'Number of adults', 'awebooking' ),
			'default'          => 1,
			'validate'         => 'required|numeric|min:1',
			'validate_label'   => esc_html__( 'Adults', 'awebooking' ),
			'sanitization_cb'  => 'absint',
		));

		$this->add_field( array(
			'id'              => 'children',
			'type'            => 'select',
			'name'            => esc_html__( 'Number of children', 'awebooking' ),
			'default'         => 0,
			'validate'        => 'required|numeric|min:0',
			'sanitization_cb' => 'absint',
		));

		$this->add_field( array(
			'id'         => 'price',
			'type'       => 'text_small',
			'name'       => esc_html__( 'Price (per night)', 'awebooking' ),
			'validate'   => 'required|numeric:min:0',
			'sanitization_cb' => 'awebooking_sanitize_price_number',
		));
	}

	/**
	 * Adds submenu page if there are plugin actions to take.
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_submenu_page
	 *
	 * @access private
	 */
	public function _register_page() {
		$page_hook = add_submenu_page( null, 'A', 'A', 'manage_options', $this->page, [ $this, 'output' ] );

		add_action( 'load-' . $page_hook, [ $this, '_no_header' ] );

		// Include CMB CSS in the head to avoid FOUC.
		add_action( "admin_print_styles-{$page_hook}", [ $this, '_enqueue_scripts' ] );
	}

	/**
	 * Page with no header.
	 *
	 * @access private
	 */
	public function _no_header() {
		$_GET['noheader'] = true;
	}

	/**
	 * Enqueue CMB2 and our styles, scripts.
	 *
	 * @access private
	 */
	public function _enqueue_scripts() {
		CMB2_hookup::enqueue_cmb_js();
		CMB2_hookup::enqueue_cmb_css();
	}

	/**
	 * //
	 *
	 * @param  [type] $field_id [description]
	 * @return [type]           [description]
	 */
	protected function get_field_value( $field_id ) {
		$field = $this->get_field( $field_id );

		if ( false === $field ) {
			return;
		}

		return $field->val_or_default( $field->value() );
	}

	protected function prepare_setup() {
		if ( empty( $_REQUEST['booking'] ) || empty( $_REQUEST['item'] ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to access this page.', 'awebooking' ), 403 );
		}

		$the_booking = new Booking( absint( $_REQUEST['booking'] ) );
		if ( ! $the_booking->exists() ) {
			wp_die( esc_html__( 'The booking not found.', 'awebooking' ) );
		}

		$item_id = absint( $_REQUEST['item'] );
		if ( ! $the_booking->has_item( $item_id ) ) {
			wp_die( esc_html__( 'You attempted to edit an item that doesn&#8217;t exist or doesn&#8217;t belong to this booking. Perhaps it was deleted?.', 'awebooking' ) );
		}

		// Fill CMB2 field value.
		foreach ( $this->prop( 'fields' ) as $field_args ) {
			$this->get_field( $field_args['id'] )->set_id( );
		}

		$this->booking = $the_booking;
		$this->booking_item = $the_booking->get_item( $item_id );
	}

	protected function handle_form() {
		if ( isset( $_POST[ $this->nonce() ] ) && wp_verify_nonce( $_POST[ $this->nonce() ], $this->nonce() ) ) {
			$input_data = $this->get_sanitized_values( $_POST );

			$room = new Room( $input_data['add_room'] );

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

				$item['name'] = $room->get_name();
				$item['check_in'] = $date_period->get_start_date()->toDateString();
				$item['check_out'] = $date_period->get_end_date()->toDateString();
				$item['adults'] = $input_data['adults'];
				$item['children'] = $input_data['children'];
				$item['room_id'] = $room->get_id();
				$item['total'] = $input_data['price'];
				$item['subtotal'] = $input_data['price'];

				$this->booking->add_item( $item );
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

		// $this->handle_form();

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
					<?php $this->show_form(); ?>

					<div class="clear"></div>
					<br>

					<a href="<?php echo esc_url( get_edit_post_link( $this->booking->get_id() ) ); ?>" class="button button-primary"><?php echo esc_html__( 'Cancel', 'awebooking' ) ?></a>
					<input class="button" type="submit" name="add_room_submit" value="Add Room" style="float: right">

				</div>
			</form>
		</div><?php
	}
}
