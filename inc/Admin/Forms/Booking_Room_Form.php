<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Dropdown;
use AweBooking\Constants;
use AweBooking\Model\Room_Type;
use AweBooking\Support\Utils as U;
use AweBooking\Model\Booking_Room_Item;

class Edit_Room_Item_Form extends Form_Abstract {
	/**
	 * The form ID.
	 *
	 * @var string
	 */
	protected $form_id = 'awebooking_edit_room_item';

	/**
	 * The room_item instance.
	 *
	 * @var \AweBooking\Model\Booking_Room_Item
	 */
	protected $room_item;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Model\Booking_Room_Item $room_item The room_item instance.
	 */
	public function __construct( Booking_Room_Item $room_item ) {
		$this->room_item = $room_item;

		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function fields() {
		$room_type = $this->room_item->resolve_room_type();

		$this->add_field([
			'id'          => 'check_in_out',
			'type'        => 'date_range',
			'name'        => esc_html__( 'Check-In / Check-Out', 'awebooking' ),
			'validate'    => 'date_period',
			'attributes'  => [ 'placeholder' => Constants::DATE_FORMAT ],
			'date_format' => Constants::DATE_FORMAT,
			'before_row'  => $this->callback_before_form(),
			'locked'      => true,
		]);

		$this->add_field([
			'id'               => 'adults',
			'type'             => 'select',
			'name'             => esc_html__( 'Number of adults', 'awebooking' ),
			'default'          => 1,
			'options'          => $this->generate_occupancy_select( $room_type, 1 ),
			'validate'         => 'required|numeric|min:1',
			'validate_label'   => esc_html__( 'Adults', 'awebooking' ),
			'sanitization_cb'  => 'absint',
		]);

		$this->add_field([
			'id'              => 'children',
			'type'            => 'select',
			'name'            => esc_html__( 'Number of children', 'awebooking' ),
			'options'         => $this->generate_occupancy_select( $room_type, 0 ),
			'default'         => 0,
			'validate'        => 'required|numeric|min:0',
			'sanitization_cb' => 'absint',
		]);

		$this->add_field([
			'id'              => 'infants',
			'type'            => 'select',
			'name'            => esc_html__( 'Number of infants', 'awebooking' ),
			'options'         => $this->generate_occupancy_select( $room_type, 0 ),
			'default'         => 0,
			'validate'        => 'required|numeric|min:0',
			'sanitization_cb' => 'absint',
		]);

		$this->add_field([
			'id'          => 'total',
			'type'        => 'text_small',
			'name'        => esc_html__( 'Total', 'awebooking' ),
			'append'      => awebooking( 'currency' )->get_symbol(),
			'validate'    => 'required|numeric|min:0',
		]);
	}

	/**
	 * Generate the select occupancy.
	 *
	 * @param  \AweBooking\Model\Room_Type $room_type //.
	 * @param  integer                     $minimum   //.
	 * @param  integer                     $selected  //.
	 * @return array
	 */
	protected function generate_occupancy_select( Room_Type $room_type, $minimum = 1, $selected = 0 ) {
		$maximum = $room_type->get_maximum_occupancy();

		$range = range( $minimum, $maximum );

		return array_combine( $range, $range );
	}

	/**
	 * Callback before print amout field.
	 *
	 * @return Closure
	 */
	protected function callback_before_form() {
		return function() {

			list( $room, $room_type ) = [ // @codingStandardsIgnoreLine
				$this->room_item->get_room_unit(), $this->room_item->resolve_room_type()
			];

			?><div class="cmb-row">
				<div class="cmb-th"><?php echo esc_html__( 'Room type', 'awebooking' ); ?></div>

				<div class="cmb-td">
					<?php if ( $room_type->exists() ) : ?>
						<strong><a href="<?php echo esc_url( $room_type->get_edit_link() ); ?>" target="_blank" style="text-decoration: none;"><?php echo esc_html( $room_type->get_title() ); ?></a></strong>
					<?php else : ?>
						<span><?php echo esc_html__( 'No room type reference', 'awebooking' ); ?></span>
					<?php endif ?>
				</div>
			</div>

			<div class="cmb-row">
				<div class="cmb-th"><?php echo esc_html__( 'Booked Room', 'awebooking' ); ?></div>

				<div class="cmb-td">
					<?php if ( $room->exists() ) : ?>
						<strong><?php echo esc_html( $room->get_name() ); ?></strong>
					<?php else : ?>
						<span><?php echo esc_html( $this->room_item->get_name() ); ?></span>
					<?php endif ?>
				</div>
			</div>
		<?php }; // @codingStandardsIgnoreLine
	}
}
