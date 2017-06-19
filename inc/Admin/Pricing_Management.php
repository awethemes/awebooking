<?php

namespace AweBooking\Admin;

use WP_Query;
use WP_List_Table;
use AweBooking\Rate;
use AweBooking\Room_Type;
use AweBooking\Rate_Pricing;
use AweBooking\Pricing\Price;
use AweBooking\Admin\Calendar\Pricing_Calendar;

class Pricing_Management extends WP_List_Table {
	/**
	 * The WP_Query instance.
	 *
	 * @var WP_Query
	 */
	protected $the_query;

	/**
	 * Pricing_Management constructor.
	 */
	public function __construct() {
		parent::__construct( [ 'plural' => 'pricing_management' ] );

		// $this->screen->add_option( 'per_page' );

		$this->the_query = $this->setup_the_query();

		// TODO:
		if (isset($_POST['action']) && 'set_pricing' === $_POST['action'] ) {

			$start_day = abkng_create_datetime( $_POST['start_date'] )->startOfDay();
			$end_day = abkng_create_datetime( $_POST['end_date'] )->startOfDay();

			$rate = new Rate( $_REQUEST['room_type'] );
			$price = new Price( $_REQUEST['price'] );

			$pricing = new Rate_Pricing( $rate, $start_day, $end_day, $price );
			$pricing->save();
		}
	}

	/**
	 * Output the page.
	 */
	public function output() {
		$this->prepare_items();

		require_once ABSPATH . 'wp-admin/admin-header.php';

		wp_enqueue_style( 'media-views' );
		wp_enqueue_script( 'media-views' );

		wp_enqueue_script( 'awebooking-pricing-calendar' );

		?><div class="wrap">
			<h1><?php esc_html_e( 'Bulk Pricing Manager', 'awebooking' ); ?></h1>

			<?php $this->display(); ?>

			<script type="text/template" id="tmpl-pricing-calendar-form">
				<form accept="" method="post" id="pricing-calendar-form">
					<div style="padding: 15px;">
					<p>{{{ data.showComments() }}}</p>

					<input type="hidden" name="room_type" value="{{ data.data_id }}">
					<input type="hidden" name="start_date" value="{{ data.startDay.format('YYYY-MM-DD') }}">
					<input type="hidden" name="end_date" value="{{ data.endDay.format('YYYY-MM-DD') }}">

					<input type="hidden" name="action" value="set_pricing">

					<label><?php echo esc_html__( 'Price:', 'awebooking' ) ?></label>
					<input type="text" name="price">
					<span><?php echo awebooking( 'currency' )->get_symbol(); ?></span>

					<button class="button" type="submit">Set price</button>
				</div>
				</form>
			</script>

			<div tabindex="0" class="pricing-calendar-modal" style="display: none;">
				<div class="media-modal-backdrop"></div>

				<div class="media-modal wp-core-ui" style="width: 600px; height: 350px; margin: auto;">
					<button type="button" class="media-modal-close"><span class="media-modal-icon"><span class="screen-reader-text">Close media panel</span></span></button>
					<div class="media-modal-content">
						<!-- Template here... -->
					</div>
				</div>
			</div>

		</div><?php
	}

	/**
	 * Get a list of columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return [
			// 'cb'   => '<input type="checkbox">',
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
		(new Pricing_Calendar( $room_type ))->display();
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @return void
	 */
	public function prepare_items() {
		/** Process bulk action */
		$this->process_bulk_action();

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
	 * @return boolean
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
