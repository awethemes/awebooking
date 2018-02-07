<?php
namespace AweBooking\Admin\List_Tables;

class Availability_List_Table extends \WP_List_Table {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct([
			'ajax'     => false,
			'singular' => 'availability',
			'plural'   => 'availabilities',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function display() {
		$this->prepare_items();

		$this->screen->render_screen_reader_content( 'heading_list' );

		?><table class="wp-list-table <?php echo esc_attr( implode( ' ', $this->get_table_classes() ) ); ?>">
			<thead>
				<tr>
					<?php $this->print_column_headers(); ?>
				</tr>
			</thead>

			<tbody id="the-list">
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>
		</table>
		<?php

		$this->display_tablenav( 'bottom' );
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
		return [
			'room_type'       => esc_html__( 'Room Type', 'awebooking' ),
			'starting_from'   => esc_html__( 'Starting From', 'awebooking' ),
			'stay_infomation' => esc_html__( 'Stay', 'awebooking' ),
			'availabe_rooms'  => esc_html__( 'Available', 'awebooking' ),
			'column_adults'   => esc_html__( 'Adults', 'awebooking' ),
			'column_children' => esc_html__( 'Children', 'awebooking' ),
			'column_infants'  => esc_html__( 'Infants', 'awebooking' ),
			'booknow'         => '',
		];
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
				echo '<p><strong>', esc_html( $room_type->get_title() ) ,'</strong></p>';

				/* translators: %d Number of rooms left */
				echo esc_html( sprintf( _n( '%d room left', '%d rooms left', $remain_rooms->count(), 'awebooking' ), $remain_rooms->count() ) );
				$this->print_rooms_debug( $room_type, $remain_rooms, $availability->get_excluded() );
				break;

			case 'stay_infomation':
				print $availability->get_stay()->as_string(); // @WPCS: XSS OK.
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
				echo '<input type="submit" class="button button-primary" name="submit[' . esc_attr( $room_type->get_id() ) . ']" value="', esc_html__( 'Book Now', 'awebooking' ) , '" />';
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
}
