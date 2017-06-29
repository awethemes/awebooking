<?php
namespace AweBooking\Admin;

use WP_List_Table;
use Carbon\Carbon;
use AweBooking\Room;
use AweBooking\Room_State;
use AweBooking\BAT\Calendar;
use AweBooking\Admin\Calendar\Yearly_Calendar;
use AweBooking\Support\Date_Period;
use AweBooking\Support\Date_Utils;

class Availability_Management extends WP_List_Table {

	protected $store;
	protected $current;

	protected $_year;

	public function __construct() {
		parent::__construct();

		$this->store = awebooking()->make( 'store.room' );
		$this->current = new Carbon;

		$current_year = date( 'Y' );
		$_year = isset( $_GET['year'] ) ? (int) $_GET['year'] : $current_year;

		if ( ! Date_Utils::is_validate_year( $_year ) ) {
			$_year = $current_year;
		}
		$this->_year = $_year;
	}

	/**
	 * Get a list of CSS classes for the WP_List_Table table tag.
	 *
	 * @return array
	 */
	protected function get_table_classes() {
		return array( 'widefat', 'fixed', $this->_args['plural'] );
	}

	public function output() {
		$this->prepare_items();

		if ( isset( $_GET['noheader'] ) && $_GET['noheader'] ) {
			require_once ABSPATH . 'wp-admin/admin-header.php';
		}

		wp_enqueue_style( 'daterangepicker' );
		wp_enqueue_script( 'daterangepicker' );
		wp_enqueue_script( 'awebooking-yearly-calendar' );

		include_once __DIR__ . '/views/page-rooms-manager.php';
	}

	public function display() {
		$screen = get_current_screen();
		?>

		<div class="wp-filter" style="margin-bottom: 0;">
			<div style="float: left; margin: 10px 0;">
				<label>From night</label>
				<input type="text" class="init-daterangepicker-start" name="datepicker-start" style="width: 100px;">

				<label>to night</label>
				<input type="text" class="init-daterangepicker-end" name="datepicker-end" style="width: 100px;">

				<div id="edit-day-options" class="form-checkboxes" style="display: inline-block;">
					<div class="form-item form-type-checkbox">
					<input type="checkbox" id="edit-day-options-1" name="day_options[]" value="1" checked="checked" class="form-checkbox">  <label class="option" for="edit-day-options-1">Mon </label>
					</div>

					<div class="form-item form-type-checkbox">
					<input type="checkbox" id="edit-day-options-2" name="day_options[]" value="2" checked="checked" class="form-checkbox">  <label class="option" for="edit-day-options-2">Tue </label>
					</div>

					<div class="form-item form-type-checkbox">
					<input type="checkbox" id="edit-day-options-3" name="day_options[]" value="3" checked="checked" class="form-checkbox">  <label class="option" for="edit-day-options-3">Wed </label>
					</div>

					<div class="form-item form-type-checkbox">
					<input type="checkbox" id="edit-day-options-4" name="day_options[]" value="4" checked="checked" class="form-checkbox">  <label class="option" for="edit-day-options-4">Thu </label>
					</div>

					<div class="form-item form-type-checkbox">
					<input type="checkbox" id="edit-day-options-5" name="day_options[]" value="5" checked="checked" class="form-checkbox">  <label class="option" for="edit-day-options-5">Fri </label>
					</div>

					<div class="form-item form-type-checkbox">
					<input type="checkbox" id="edit-day-options-6" name="day_options[]" value="6" checked="checked" class="form-checkbox">  <label class="option" for="edit-day-options-6">Sat </label>
					</div>

					<div class="form-item form-type-checkbox">
					<input type="checkbox" id="edit-day-options-0" name="day_options[]" value="0" checked="checked" class="form-checkbox">  <label class="option" for="edit-day-options-1">Sun </label>
					</div>
				</div>

				<select name="state">
					<option value="0">Available</option>
					<option value="1">Unavailable</option>
				</select>

				<input type="hidden" name="action" value="bulk-update">
				<button class="button" type="submit"><?php echo esc_html__( 'Bulk Update', 'awebooking' ) ?></button>
			</div>

			<div class="search-form search-plugins">
				<label>
					<span class="screen-reader-text">Search</span>
					<!-- <input type="search" name="s" value="" class="wp-filter-search" placeholder="Search..." aria-describedby="live-search-desc"> -->
				</label>

				<input type="submit" id="search-submit" class="button hide-if-js" value="Search Plugins">
			</div>

			<?php $this->room_type_dropdown(); ?>

			<div class="" style="position: relative; float: right;">
				<?php
				$_year = $this->_year;
				$years = [ $_year - 1, $_year, $_year + 1 ];
				?>
				<button type="button" class="button drawer-toggle toggle-year" aria-expanded="false"><?php echo $_year; ?></button>

				<ul class="split-button-body">
					<?php foreach ( $years as $year ) : ?>
						<li>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=manager-awebooking&amp;=' . $screen->parent_base . '&year=' . $year ) ); ?>"><?php echo esc_html( $year ); ?></a>
						</li>
					<?php endforeach ?>
				</ul>
			</div>

			<style type="text/css">
				.drawer-toggle.toggle-year:before {
					color: #333;
					font-size: 16px;
					content: "\f145";
				}
				.form-item {
					display: inline-block;
				}
			</style>

		</div>

		<?php parent::display();
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$room_type = isset( $_REQUEST['room-type'] ) ? absint( $_REQUEST['room-type'] ) : 0;

		$per_page = $this->get_items_per_page( 'customers_per_page', 15 );

		$this->set_pagination_args( [
			'total_items' => $this->store->count( $room_type ),
			'per_page'    => $per_page,
		]);

		$items = $this->store->query([
			'per_page'    => $per_page,
			'page_number' => $this->get_pagenum(),
			'room_type'   => $room_type,
		]);

		$this->items = array_map( function( $item ) {
			return new Room( $item['id'] );
		}, $items);
	}

	/**
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		if ( 'top' === $which ) {
			echo '<div class="alignleft actions">'; ?>

			<ul class="awebooking-list-item">
				<li>
					<span class="awebooking-availability available"></span>
					<span><?php echo esc_html__( 'Available', 'awebooking' ) ?></span>
				</li>

				<li>
					<span class="awebooking-availability unavailable"></span>
					<span><?php echo esc_html__( 'Unavailable', 'awebooking' ) ?></span>
				</li>

				<li>
					<span class="awebooking-availability pending"></span>
					<span><?php echo esc_html__( 'Pending', 'awebooking' ) ?></span>
				</li>

				<li>
					<span class="awebooking-availability booked"></span>
					<span><?php echo esc_html__( 'Booked', 'awebooking' ) ?></span>
				</li>
			</ul>

			<?php
			// $this->categories_dropdown();
			// submit_button( __( 'Filter' ), '', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
			echo '</div>';
		}
	}

	/**
	 * Displays a categories drop-down for filtering on the Posts list table.
	 */
	protected function room_type_dropdown() {
		$screen = get_current_screen();

		$room_type = wp_data( 'posts', [
			'post_type' => 'room_type',
		]);

		$current_room_type = '';

		if ( isset( $_REQUEST['room-type'] ) && isset( $room_type[ $_REQUEST['room-type'] ] ) ) {
			$current_room_type = $room_type[ $_REQUEST['room-type'] ];
		} else {
			$current_room_type = 'All Room Types';
		}

		?>
		<div class="" style="position: relative; float: right;">
		<button type="button" class="button drawer-toggle" aria-expanded="false"><?php echo $current_room_type; ?></button>

		<ul class="split-button-body">
			<li>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=manager-awebooking&amp;=' . $screen->parent_base ) ); ?>"><?php echo esc_html__( 'All Room Types', 'awebooking' ); ?></a>
			</li>

			<?php foreach ( $room_type as $id => $name ) : ?>
				<li>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=manager-awebooking&amp;=' . $screen->parent_base . '&amp;room-type=' . $id ) ); ?>"><?php echo esc_html( $name ); ?></a>
				</li>
			<?php endforeach ?>
		</ul>
		</div><?php
	}

	/**
	 * Get a list of columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return [
			'cb'   => '<input type="checkbox">',
			// 'room' => esc_html__( 'Room', 'awebooking' ),
			'calendar' => esc_html__( 'Calendar', 'awebooking' ),
		];
	}

	/**
	 * Handles the default column output.
	 *
	 * @param \AweBooking\Room $room        The Room object.
	 * @param string           $column_name Column name.
	 */
	protected function column_default( $room, $column_name ) {
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
		$calendar = new Yearly_Calendar( $this->_year, $item );
		return $calendar->display();
	}

	public function process_bulk_action() {
		if ( ! empty( $_POST ) && ! empty( $_POST['bulk-update'] ) && 'bulk-update' === $this->current_action() ) {

			$ids = $_POST['bulk-update'];

			$only_days = [];
			if ( isset( $_POST['day_options'] ) && is_array( $_POST['day_options'] ) ) {
				$only_days = array_map( 'absint', $_POST['day_options'] );
			}

			try {
				$date_period = new Date_Period( $_POST['datepicker-start'], $_POST['datepicker-end'] );

				foreach ( $ids as $id ) {
					$room = new Room( (int) $id );

					awebooking( 'concierge' )->set_room_state( $room, $date_period, $_REQUEST['state'], [
						'only_days' => $only_days,
					]);
				}
			} catch (\Exception $e) {
				// ...
			}
		}
	}
}
