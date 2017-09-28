<?php
namespace AweBooking\Admin\Pages;

use WP_List_Table;
use AweBooking\Concierge;
use AweBooking\Hotel\Room;
use AweBooking\Booking\Calendar;
use AweBooking\Admin\Calendar\Yearly_Calendar;
use AweBooking\Support\Period;
use AweBooking\Support\Carbonate;

class Availability_Management extends WP_List_Table {

	protected $store;
	protected $current;
	protected $room_type;

	protected $_year;

	public function __construct() {
		parent::__construct();

		$this->current = new Carbonate;

		$current_year = date( 'Y' );
		$_year = isset( $_GET['year'] ) ? (int) $_GET['year'] : $current_year;

		if ( ! Carbonate::is_valid_year( $_year ) ) {
			$_year = $current_year;
		}

		$this->_year = $_year;
	}

	public function output() {
		$this->prepare_items();

		if ( isset( $_GET['noheader'] ) && $_GET['noheader'] ) {
			require_once ABSPATH . 'wp-admin/admin-header.php';
		}

		include_once trailingslashit( __DIR__ ) . 'views/html-page-availability-management.php';
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page  = $this->get_items_per_page( 'customers_per_page', 15 );
		$room_type = isset( $_REQUEST['room-type'] ) ? absint( $_REQUEST['room-type'] ) : 0;
		$this->room_type = $room_type;

		// ------------------------------------------------------
		global $wpdb;
		$select_query = "SELECT * FROM `{$wpdb->prefix}awebooking_rooms` AS `room`";
		$join_clause = " INNER JOIN `{$wpdb->posts}` AS `post` ON (post.ID = room.room_type AND post.post_status = 'publish' AND post.post_type = 'room_type')";

		$where_clause = '';
		if ( ! empty( $room_type ) ) {
			$where_clause = ' WHERE room.room_type = ' . esc_sql( $room_type );
		}

		$offset = ( $this->get_pagenum() - 1 ) * $per_page;
		$order_limit_clause = ' ORDER BY post.ID DESC, room.id ASC LIMIT ' . esc_sql( $per_page ) . ' OFFSET ' . esc_sql( $offset );

		// @codingStandardsIgnoreLine
		$results = $wpdb->get_results( $select_query . $join_clause . $where_clause . $order_limit_clause , ARRAY_A );

		// @codingStandardsIgnoreLine
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM `{$wpdb->prefix}awebooking_rooms` AS `room` " . $join_clause . $where_clause );

		// ------------------------------------------------------
		$this->set_pagination_args( [
			'total_items' => intval( $count ),
			'per_page'    => $per_page,
		]);

		$this->items = awebooking_map_instance(
			wp_list_pluck( $results, 'id' ), Room::class
		);
	}

	/**
	 * //
	 *
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		if ( 'top' === $which ) { ?>
			<div class="alignleft actions">
				<ul class="awebooking-list-item">
					<li>
						<span class="awebooking-availability available"></span>
						<span><?php echo esc_html__( 'Available', 'awebooking' ); ?></span>
					</li>

					<li>
						<span class="awebooking-availability unavailable"></span>
						<span><?php echo esc_html__( 'Unavailable', 'awebooking' ); ?></span>
					</li>

					<li>
						<span class="awebooking-availability pending"></span>
						<span><?php echo esc_html__( 'Pending', 'awebooking' ); ?></span>
					</li>

					<li>
						<span class="awebooking-availability booked"></span>
						<span><?php echo esc_html__( 'Booked', 'awebooking' ); ?></span>
					</li>
				</ul>
			</div><?php
		}
	}

	/**
	 * Get a list of columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return [
			'calendar' => esc_html__( 'Calendar', 'awebooking' ),
		];
	}

	public function get_primary_column() {
		return 'calendar';
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item //.
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-update[]" value="%s" />', $item->get_id()
		);
	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data.
	 *
	 * @return string
	 */
	public function column_room( $item ) {
		if ( $item->get_name() ) {
			return $item->get_name() . ' (#' . $item->get_id() . ')';
		}

		return '#' . $item->get_id();
	}

	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data.
	 *
	 * @return string
	 */
	public function column_calendar( $item ) {
		$calendar = new Yearly_Calendar( $item, $this->_year );
		return $calendar->display();
	}

	public function process_bulk_action() {
		if ( ! empty( $_POST ) && ! empty( $_POST['bulk-update'] ) && 'bulk-update' === $this->current_action() ) {
			$ids = $_POST['bulk-update'];
			if ( empty( $ids ) ) {
				return;
			}

			$only_days = [];
			if ( isset( $_POST['day_options'] ) && is_array( $_POST['day_options'] ) ) {
				$only_days = array_map( 'absint', $_POST['day_options'] );
			}

			try {
				$date = new Period( $_POST['datepicker-start'], $_POST['datepicker-end'] );

				foreach ( $ids as $id ) {
					$room = new Room( (int) $id );

					Concierge::set_availability( $room, $date, $_REQUEST['state'], [
						'only_days' => $only_days,
					]);
				}
			} catch (Exception $e) {
				// ...
			}
		}
	}
}
