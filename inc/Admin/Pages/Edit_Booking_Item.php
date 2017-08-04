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
use AweBooking\Admin\Forms\Edit_Booking_Form;

class Edit_Booking_Item {
	/**
	 * The admin page ID.
	 *
	 * @var string
	 */
	protected $page = 'awebooking-edit-item';

	protected $booking;
	protected $form;

	/**
	 * Add booking item constructor.
	 */
	public function __construct( Edit_Booking_Form $form ) {
		$this->form = $form;
	}

	/**
	 * Init page hooks.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, '_register_page' ) );
	}

	/**
	 * Adds submenu page if there are plugin actions to take.
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_submenu_page
	 *
	 * @access private
	 */
	public function _register_page() {
		$page_hook = add_submenu_page( null, '', '', 'manage_options', $this->page, [ $this, 'output' ] );

		add_action( 'load-' . $page_hook, function() {
			$_GET['noheader'] = true;
		});

		// Include CMB CSS in the head to avoid FOUC.
		add_action( "admin_print_styles-{$page_hook}", [ $this->form, 'enqueue_scripts' ] );
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

		$this->booking = $the_booking;
		$this->booking_item = $the_booking->get_item( $item_id );
		$the_room = $this->booking_item->get_booking_room();

		$room_type = $the_room->get_room_type();
		$max_adults = $room_type->get_number_adults() + $room_type->get_max_adults();
		$max_children = $room_type->get_number_children() + $room_type->get_max_children();

		$a = range( 1, $max_adults );
		$b = range( 0, $max_children );
		$this->form->get_field( 'adults' )->set_prop( 'options', array_combine( $a, $a ) );
		$this->form->get_field( 'children' )->set_prop( 'options', array_combine( $b, $b ) );

		// Fill CMB2 field value.
		$this->form->get_field( 'price' )->value = $this->booking_item->get_total()->get_amount();
		$this->form->get_field( 'adults' )->value = $this->booking_item->get_adults();
		$this->form->get_field( 'children' )->value = $this->booking_item->get_children();

		$this->form->get_field( 'check_in_out' )->value = [
			$this->booking_item->get_check_in(),
			$this->booking_item->get_check_out(),
		];
	}

	protected function handle_form() {
		$nonce = $this->form->nonce();

		if ( isset( $_POST[ $nonce ] ) && wp_verify_nonce( $_POST[ $nonce ], $nonce ) ) {
			$input_data = $this->form->get_sanitized_values( $_POST );

			$working_url = add_query_arg([
					'booking' => $this->booking->get_id(),
					'item'    => $this->booking_item->get_id(),
				], admin_url( 'admin.php?page=awebooking-edit-item' )
			);

			if ( ! empty( $input_data ) ) {
				// Fill the input data then save them.
				$this->booking_item['adults'] = $input_data['adults'];
				$this->booking_item['children'] = $input_data['children'];

				if ( isset( $input_data['price'] ) ) {
					$this->booking_item['total'] = $input_data['price'];
				}

				if ( isset( $input_data['check_in_out'] ) ) {
					$this->booking_item['check_in'] = $input_data['check_in_out'][0];
					$this->booking_item['check_out'] = $input_data['check_in_out'][1];
				}

				try {
					$this->booking_item->save();
				} catch ( \Exception $e ) {
					$this->add_validation_error( 'check_in_out', $e->getMessage() );
					// wp_redirect( $working_url );
					return;
				}
			}

			wp_redirect(
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

		$this->handle_form();

		require_once ABSPATH . 'wp-admin/admin-header.php';

		?><div class="wrap cmb2-options-page">
			<h1 class="wp-heading-inline" style="margin-bottom: 15px;">Edit Item "<?php echo esc_html( $this->booking_item->get_name() ); ?>"</h1>
			<hr class="wp-header-end">

			<style type="text/css">
				.cmb2-options-page .cmb2-wrap,
				.cmb2-options-page .cmb2-wrap select {
					width: 100%;
				}
			</style>

			<form method="POST" action="">
				<input type="hidden" name="page" value="<?php echo esc_attr( $this->page ); ?>">

				<div class="postbox" style="width: 270px; float: left; margin-right: 15px;">
					<div class="" style="padding: 0 15px;">
						<p>
							<strong><?php echo esc_html__( 'Booking Reference:', 'awebooking' ) ?></strong>
							<span><?php echo $this->booking->get_id(); ?></span>
						</p>

						<p>
							<strong><?php echo esc_html__( 'Room Type:', 'awebooking' ) ?></strong>
							<span><?php echo $this->booking_item->get_name(); ?></span>
						</p>

						<p>
							<strong><?php echo esc_html__( 'Booked Room:', 'awebooking' ) ?></strong>
							<span><?php echo $this->booking_item->get_room_id(); ?></span>
						</p>
					</div>
				</div>

				<div class="" style="width: 475px; float: left; margin-right: 15px;">
					<?php $this->form->show_form(); ?>

					<div class="clear"></div>
					<br>

					<a href="<?php echo esc_url( get_edit_post_link( $this->booking->get_id() ) ); ?>" class="button button-primary"><?php echo esc_html__( 'Cancel', 'awebooking' ) ?></a>
					<input class="button" type="submit" name="add_room_submit" value="Update" style="float: right">

				</div>
			</form>
		</div><?php
	}
}
