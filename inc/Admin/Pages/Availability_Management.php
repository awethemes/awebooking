<?php
namespace AweBooking\Admin\Pages;

use WP_Query;
use WP_List_Table;
use AweBooking\AweBooking;
use AweBooking\Support\Carbonate;
use AweBooking\Hotel\Room_Type;

class Availability_Management extends WP_List_Table {
	/**
	 * The WP_Query instance.
	 *
	 * @var WP_Query
	 */
	protected $the_query;

	/**
	 * The year we will working on.
	 *
	 * @var int
	 */
	protected $year;

	/**
	 * The month we will working on.
	 *
	 * @var int
	 */
	protected $month;

	/**
	 * Pricing_Management constructor.
	 */
	public function __construct() {
		parent::__construct( [ 'plural' => 'availability_management' ] );

		$current_year = (int) date( 'Y' );
		$year = isset( $_GET['year'] ) ? (int) $_GET['year'] : $current_year;

		if ( ! Carbonate::is_valid_year( $year ) ) {
			$year = $current_year;
		}

		$this->year = $year;

		$month = ( isset( $_GET['month'] ) && $_GET['month'] >= 1 && $_GET['month'] <= 12 ) ? (int) $_GET['month'] : absint( date( 'n' ) );

		$this->month = $month;
	}

	/**
	 * Output the page.
	 */
	public function output() {
		$this->prepare_items();

		require_once ABSPATH . 'wp-admin/admin-header.php';

		echo '<div class="wrap">';
		echo '<h1 class="wp-heading-inline">' . esc_html__( 'Manager Availability', 'awebooking' ) . '</h1>';

		if ( isset( $_REQUEST['room_type'] ) ) {
			include_once trailingslashit( __DIR__ ) . 'views/html-page-availability-management.php';
		} else {
			$this->display();
		}

		echo '</div>';
	}

	/**
	 * Get a list of columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return [
			'room_type' => esc_html__( 'Room Type', 'awebooking' ),
		];
	}

	/**
	 * Public wrapper for WP_List_Table::get_default_primary_column_name().
	 *
	 * @since 4.4.0
	 * @access public
	 *
	 * @return string Name of the default primary column.
	 */
	public function get_primary_column() {
		return 'room_type';
	}

	/**
	 * Build the bulk edit checkbox.
	 *
	 * @param Room_Type $room_type Room_Type instance.
	 * @return string
	 */
	public function column_room_type( $room_type ) {
		$edit_link = admin_url( 'admin.php?page=awebooking-availability&amp;room_type=' . $room_type->get_id() );
		printf( '<a href="%1$s">%2$s</a>', esc_url( $edit_link ), esc_html( $room_type->get_title() ) );
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @return void
	 */
	public function prepare_items() {
		$this->the_query = $this->setup_the_query();

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
		return new WP_Query([
			'post_type'           => AweBooking::ROOM_TYPE,
			'posts_per_page'      => $this->get_items_per_page( 'awebooking/management_per_page', 15 ),
			'paged'               => $this->get_pagenum(),
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => false,
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
}
