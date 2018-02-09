<?php
namespace AweBooking\Admin\List_Tables;

use AweBooking\Reservation\Reservation;

class Availability_List_Table extends \WP_List_Table {
	/**
	 * The reservation instance.
	 *
	 * @var \AweBooking\Reservation\Reservation
	 */
	protected $reservation;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Reservation $reservation //.
	 */
	public function __construct( Reservation $reservation ) {
		$this->reservation = $reservation;

		parent::__construct([
			'singular' => 'availability',
			'plural'   => 'availabilities',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function prepare_items() {
		$items_per_page = 10;

		$this->set_pagination_args( [
			'per_page'    => $items_per_page,
			'total_items' => $this->items->count(),
		] );

		$this->items = $this->items->forPage( $this->get_pagenum(), $items_per_page );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_columns() {
		$columns = [
			'room_type'       => esc_html__( 'Room Type', 'awebooking' ),
			'availabe_rooms'  => esc_html__( 'Room', 'awebooking' ),
			'column_adults'   => esc_html__( 'Adults', 'awebooking' ),
			'column_children' => esc_html__( 'Children', 'awebooking' ),
			'column_infants'  => esc_html__( 'Infants', 'awebooking' ),
			'starting_from'   => esc_html__( 'Price', 'awebooking' ),
			'booknow'         => '',
		];

		if ( ! awebooking( 'setting' )->is_children_bookable() ) {
			unset( $columns['column_children'] );
		}

		if ( ! awebooking( 'setting' )->is_infants_bookable() ) {
			unset( $columns['column_infants'] );
		}

		return $columns;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function column_default( $availability, $column_name ) {
		$room_type = $availability->get_room_type();
		$remain_rooms = $availability->remain_rooms();

		// The input prefix.
		$input_prefix = 'reservation_room[' . $room_type->get_id() . ']';

		switch ( $column_name ) {
			case 'room_type':
				echo '<strong>', esc_html( $room_type->get_title() ) ,'</strong>';

				echo '<div class="afloat-right row-actions">';
				/* translators: %d Number of rooms left */
				echo esc_html( sprintf( _n( '%d room left', '%d rooms left', $remain_rooms->count(), 'awebooking' ), $remain_rooms->count() ) );
				$this->print_rooms_debug( $room_type, $remain_rooms, $availability->get_excluded() );
				echo '</div>';
				break;

			case 'availabe_rooms':
				$rooms = $remain_rooms->pluck( 'room' )->pluck( 'name', 'id' )->all();

				$this->print_select_options( $input_prefix . '[room_unit]', $rooms );
				break;

			case 'column_adults':
				$this->print_select_number( $input_prefix . '[adults]', 1, $room_type->get_maximum_occupancy() );
				break;

			case 'column_children':
				$this->print_select_number( $input_prefix . '[children]', 0, $room_type->get_maximum_occupancy() );
				break;

			case 'column_infants':
				$this->print_select_number( $input_prefix . '[infants]', 0, $room_type->get_maximum_occupancy() );
				break;

			case 'booknow':
				echo '<div class="row-actions"><input type="submit" class="button button-primary" name="submit[' . esc_attr( $room_type->get_id() ) . ']" value="', esc_html__( 'Book Now', 'awebooking' ) , '" /></div>';
				break;
		}
	}

	/**
	 * Print the remain rooms debug.
	 *
	 * @param  \AweBooking\Model\Room_Type    $room_type    The room_type.
	 * @param  \AweBooking\Support\Collection $remain_rooms The remain_rooms.
	 * @param  \AweBooking\Support\Collection $reject_rooms The reject_rooms.
	 * @return void
	 */
	protected function print_rooms_debug( $room_type, $remain_rooms, $reject_rooms ) {
		$popup_id = 'popup_debug_room_type_' . $room_type->get_id();

		echo '<a href="#' . esc_attr( $popup_id ) . '" data-toggle="awebooking-popup" data-placement="right"><span class="dashicons dashicons-info"></span></a>';

		echo '<div class="awebooking-debug-rooms__dialog awebooking-dialog-contents" id="' . esc_attr( $popup_id ) . '" title="' . esc_html__( 'Room List', 'awebooking' ) . '" style="display: none;">';
		echo '<table class="awebooking-debug-rooms__table">';

		foreach ( $remain_rooms as $room_info ) {
			echo '<tr class="awebooking-debug-rooms--success">';
			echo '<th><span class="dashicons dashicons-yes"></span>', esc_html( $room_info['room']->get_name() ) ,'</th>';
			echo '<td>', esc_html( $room_info['reason_message'] ) ,'</td>';
			echo '</tr>';
		}

		foreach ( $reject_rooms as $room_info ) {
			echo '<tr class="awebooking-debug-rooms--failure">';
			echo '<th><span class="dashicons dashicons-no-alt"></span>', esc_html( $room_info['room']->get_name() ) ,'</th>';
			echo '<td>', esc_html( $room_info['reason_message'] ) ,'</td>';
			echo '</tr>';
		}

		echo '</table></div>';
	}

	/**
	 * Print the select by given a range.
	 *
	 * @param  string  $select_id The select ID.
	 * @param  integer $min       Min.
	 * @param  integer $max       Max.
	 * @return void
	 */
	protected function print_select_number( $select_id, $min, $max ) {
		$max = $max < $min ? $min : $max;

		echo '<select name="' . esc_attr( $select_id ) . '" id="' . esc_attr( sanitize_key( $select_id ) ) . '">';
		for ( $i = $min; $i <= $max; $i++ ) {
			echo '<option value="' . esc_attr( $i ) . '">' . esc_html( $i ) . '</option>';
		}

		echo '</select>';
	}

	/**
	 * Print the select by given a range.
	 *
	 * @param  string $select_id The select ID.
	 * @param  array  $options   Select options.
	 * @return void
	 */
	protected function print_select_options( $select_id, $options ) {
		echo '<select name="' . esc_attr( $select_id ) . '" id="' . esc_attr( sanitize_key( $select_id ) ) . '">';
		foreach ( $options as $key => $value ) {
			echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
		}

		echo '</select>';
	}

	/**
	 * {@inheritdoc}
	 */
	public function display() {
		$this->prepare_items();

		$this->screen->render_screen_reader_content( 'heading_list' );

		$stay = $this->reservation->get_stay();

		?>
		<input type="hidden" name="check_in" value="<?php echo esc_attr( $stay->get_check_in()->toDateString() ); ?>">
		<input type="hidden" name="check_out" value="<?php echo esc_attr( $stay->get_check_out()->toDateString() ); ?>">

		<div class="tablenav">
			<div class="alignleft actions">
				<span><?php echo esc_html__( 'Searching for:', 'awebooking' ); ?></span>
				<strong><?php printf( _n( '%s night', '%s nights', $stay->nights(), 'awebooking' ), esc_html( $stay->nights() ) ); // @codingStandardsIgnoreLine ?></strong>,
				<span><?php echo wp_kses_post( $stay->as_string() ); ?></span>
			</div>
		</div>

		<table class="wp-list-table <?php echo esc_attr( implode( ' ', $this->get_table_classes() ) ); ?>">
			<thead>
				<tr><?php $this->print_column_headers(); ?></tr>
			</thead>

			<tbody id="the-list">
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>
		</table><?php // @codingStandardsIgnoreLine

		$this->display_tablenav( 'bottom' );
	}
}
