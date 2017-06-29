<?php
namespace AweBooking\Admin;

use WP_Query;
use WP_List_Table;
use AweBooking\Rate;
use AweBooking\Room_Type;
use AweBooking\Rate_Pricing;
use AweBooking\Pricing\Price;
use AweBooking\Admin\Calendar\Pricing_Calendar;
use AweBooking\Support\Date_Utils;
use AweBooking\Support\Date_Period;

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



		$current_year = date( 'Y' );
		$_year = isset( $_GET['year'] ) ? (int) $_GET['year'] : $current_year;

		if ( ! Date_Utils::is_validate_year( $_year ) ) {
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

		wp_enqueue_style( 'media-views' );
		wp_enqueue_script( 'media-views' );

		wp_enqueue_style( 'daterangepicker' );
		wp_enqueue_script( 'daterangepicker' );

		wp_enqueue_script( 'awebooking-pricing-calendar' );

		?><div class="wrap">
			<h1><?php esc_html_e( 'Bulk Pricing Manager', 'awebooking' ); ?></h1>

			<form action="" method="POST">

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

					<input type="number" name="bulk-price" style="width: 100px;">

					<input type="hidden" name="action" value="bulk-update">
					<button class="button" type="submit"><?php echo esc_html__( 'Bulk Update', 'awebooking' ) ?></button>
				</div>

				<div class="" style="position: relative; float: right;">
					<?php
					$screen = get_current_screen();
					$_year = $this->_year;
					$years = [ $_year - 1, $_year, $_year + 1 ];
					?>
					<button type="button" class="button drawer-toggle toggle-year" aria-expanded="false"><?php echo $_year; ?></button>

					<ul class="split-button-body">
						<?php foreach ( $years as $year ) : ?>
							<li>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=manager-pricing&amp;=' . $screen->parent_base . '&year=' . $year ) ); ?>"><?php echo esc_html( $year ); ?></a>
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

			<script type="text/javascript">
			jQuery(function($) {
				$('.init-daterangepicker-start').daterangepicker({
					showDropdowns: true,
					singleDatePicker: true,
					locale: { format: 'YYYY-MM-DD' }
				});

				$('.init-daterangepicker-end').daterangepicker({
					showDropdowns: true,
					singleDatePicker: true,
					locale: { format: 'YYYY-MM-DD' }
				});
			});
			</script>

		</form>
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

	public function process_action() {
		// TODO: ...
		if ( isset( $_POST['action'] ) && 'set_pricing' === $_POST['action'] ) {
			if ( empty( $_REQUEST['room_type'] ) ) {
				return;
			}

			try {
				$price = new Price( sanitize_text_field( wp_unslash( $_POST['price'] ) ) );

				$date_period = new Date_Period(
					sanitize_text_field( wp_unslash( $_POST['start_date'] ) ),
					sanitize_text_field( wp_unslash( $_POST['end_date'] ) ),
					false
				);

				$rate = new Rate( absint( $_REQUEST['room_type'] ) );

				awebooking( 'concierge' )->set_room_price( $rate, $date_period, $price );
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
				$date_period = new Date_Period( $_POST['datepicker-start'], $_POST['datepicker-end'], false );
				$price = new Price( sanitize_text_field( wp_unslash( $_REQUEST['bulk-price'] ) ) );

				foreach ( $ids as $id ) {
					$rate = new Rate( (int) $id );

					awebooking( 'concierge' )->set_room_price( $rate, $date_period, $price, [
						'only_days' => $only_days,
					]);
				}
			} catch ( \Exception $e ) {
				// ...
			}
		}
	}
}
