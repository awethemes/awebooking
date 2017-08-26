<?php
namespace AweBooking\Admin;

use WP_Query;
use WP_List_Table;
use AweBooking\Concierge;
use AweBooking\Pricing\Rate;
use AweBooking\Hotel\Room_Type;
use AweBooking\Pricing\Price;
use AweBooking\Admin\Calendar\Pricing_Calendar;
use AweBooking\Support\Carbonate;
use AweBooking\Support\Period;

class Pricing_Management extends WP_List_Table {
	/**
	 * The WP_Query instance.
	 *
	 * @var WP_Query
	 */
	protected $the_query;

	protected $_year;

	/**
	 * Pricing_Management constructor.
	 */
	public function __construct() {
		parent::__construct( [ 'plural' => 'pricing_management' ] );

		$current_year = (int) date( 'Y' );
		$_year = isset( $_GET['year'] ) ? (int) $_GET['year'] : $current_year;

		if ( ! Carbonate::is_valid_year( $_year ) ) {
			$_year = $current_year;
		}

		$this->_year = $_year;

		$this->the_query = $this->setup_the_query();
	}

	/**
	 * Output the page.
	 */
	public function output() {
		$this->prepare_items();

		require_once ABSPATH . 'wp-admin/admin-header.php';

		include trailingslashit( __DIR__ ) . 'views/html-page-pricing-management.php';
	}

	/**
	 * Get a list of columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return [
			'cb'   => '<input type="checkbox">',
			'calendar' => esc_html__( 'Calendar', 'awebooking' ),
		];
	}

	/**
	 * Build the bulk edit checkbox.
	 *
	 * @param Room_Type $room_type Room_Type instance.
	 * @return string
	 */
	public function column_cb( $room_type ) {
		return sprintf(
			'<input type="checkbox" name="bulk-update[]" value="%s" />', $room_type->get_id()
		);
	}

	/**
	 * Build the calendar column.
	 *
	 * @param Room_Type $room_type Room_Type instance.
	 * @return void
	 */
	public function column_calendar( $room_type ) {
		(new Pricing_Calendar( $room_type, $this->_year ))->display();
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @return void
	 */
	public function prepare_items() {
		/** Process bulk action */
		$this->process_bulk_action();

		$this->process_action();

		$this->set_pagination_args([
			'total_items' => $this->the_query->found_posts,
			'total_pages' => $this->the_query->max_num_pages,
			'per_page'    => $this->the_query->query_vars['posts_per_page'],
		]);
	}

	/**
	 * Setup the WP_Query
	 *
	 * @return WP_Query
	 */
	protected function setup_the_query() {
		$per_page = $this->get_items_per_page( 'awebooking/pricing_management_per_page', 20 );

		return new WP_Query([
			'post_type'      => 'room_type',
			'posts_per_page' => $per_page,
			'paged'          => $this->get_pagenum(),
			'post_status'    => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		]);
	}

	/**
	 * Whether the table has items to display or not.
	 *
	 * @return bool
	 */
	public function has_items() {
		return $this->the_query->have_posts();
	}

	/**
	 * Generate the table rows.
	 */
	public function display_rows() {

		while ( $this->the_query->have_posts() ) : $this->the_query->the_post();
			$room_type = new Room_Type( $this->the_query->post ); ?>

			<tr id="post-<?php echo esc_attr( $room_type->get_id() ); ?>">
				<?php $this->single_row_columns( $room_type ); ?>
			</tr>
		<?php endwhile;

		wp_reset_postdata();
	}

	public function process_action() {
		// TODO: ...
		if ( isset( $_POST['action'] ) && 'set_pricing' === $_POST['action'] ) {
			if ( empty( $_REQUEST['room_type'] ) ) {
				return;
			}

			try {


				$price = new Price( sanitize_text_field( wp_unslash( $_POST['price'] ) ) );

				$period = new Period(
					sanitize_text_field( wp_unslash( $_POST['start_date'] ) ),
					sanitize_text_field( wp_unslash( $_POST['end_date'] ) ),
					false
				);

				$rate = new Rate( absint( $_REQUEST['room_type'] ) );

				Concierge::set_room_price( $rate, $period, $price );
			} catch ( \Exception $e ) {
				// ...
			}
		}
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
				$period = new Period( $_POST['datepicker-start'], $_POST['datepicker-end'], false );
				$price = new Price( sanitize_text_field( wp_unslash( $_REQUEST['bulk-price'] ) ) );

				foreach ( $ids as $id ) {
					$rate = new Rate( (int) $id );

					Concierge::set_room_price( $rate, $period, $price, [
						'only_days' => $only_days,
					]);
				}
			} catch ( \Exception $e ) {
				// ...
			}
		}
	}
}
