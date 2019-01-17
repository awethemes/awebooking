<?php

namespace AweBooking\Admin\List_Tables;

use AweBooking\Constants;

class Booking_List_Table extends Abstract_List_Table {
	/**
	 * The booking in the current loop.
	 *
	 * @var \AweBooking\Model\Booking
	 */
	protected $booking;

	/**
	 * The post type name.
	 *
	 * @var string
	 */
	protected $list_table = Constants::BOOKING;

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'parse_query', [ $this, 'search_custom_fields' ] );
		add_filter( 'get_search_query', [ $this, 'correct_search_label' ] );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_row_actions( $actions, $post ) {
		return []; // No row actions.
	}

	/**
	 * {@inheritdoc}
	 */
	protected function get_primary_column() {
		return 'booking_number';
	}

	/**
	 * {@inheritdoc}
	 */
	public function define_columns( $columns ) {
		if ( empty( $columns ) && ! is_array( $columns ) ) {
			$columns = [];
		}

		unset( $columns['title'], $columns['comments'], $columns['date'] );

		return array_merge( $columns, [
			'booking_number'    => esc_html__( 'Booking', 'awebooking' ),
			'booking_status'    => esc_html__( 'Status', 'awebooking' ),
			'booking_nights'    => '<span class="tippy" title="' . esc_html__( 'Nights', 'awebooking' ) . '"><i class="aficon aficon-moon"></i><span class="screen-reader-text">' . esc_html__( 'Nights', 'awebooking' ) . '</span></span>',
			'booking_check_in'  => esc_html__( 'Check-In', 'awebooking' ),
			'booking_check_out' => esc_html__( 'Check-Out', 'awebooking' ),
			'booking_summary'   => esc_html__( 'Summary', 'awebooking' ),
			'booking_total'     => esc_html__( 'Total', 'awebooking' ),
			'booking_paid'      => esc_html__( 'Paid', 'awebooking' ),
			'booking_date'      => esc_html__( 'Date', 'awebooking' ),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function define_sortable_columns( $columns ) {
		return array_merge( $columns, [
			'booking_number' => 'ID',
			'booking_total'  => '_total',
			'booking_date'   => 'date',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function prepare_row_data( $post_id ) {
		global $the_booking;

		if ( is_null( $this->booking ) || $this->booking->get_id() !== (int) $post_id ) {
			$the_booking   = abrs_get_booking( $post_id );
			$this->booking = $the_booking;
		}
	}

	/**
	 * Display column: booking_number.
	 *
	 * @return void
	 */
	protected function display_booking_number_column() {
		global $the_booking;

		if ( $the_booking->get_status() === 'trash' ) {

			echo '<strong>#' . esc_attr( $the_booking->get_booking_number() ) . '</strong>';

		} else {
			if ( $the_booking['customer_id'] ) {
				$userdata = get_userdata( $the_booking['customer_id'] );
				$username = $userdata ? sprintf( '<a href="user-edit.php?user_id=%d">%s</a>', absint( $userdata->ID ), ucwords( $userdata->display_name ) ) : '';
			} elseif ( $the_booking['customer_first_name'] || $the_booking['customer_last_name'] ) {
				/* translators: 1 First Name, 2 Last Name */
				$username = trim( sprintf( _x( '%1$s %2$s', 'full name', 'awebooking' ), $the_booking->get( 'customer_first_name' ), $the_booking->get( 'customer_last_name' ) ) );
			} elseif ( $the_booking['customer_company'] ) {
				$username = trim( $the_booking->get( 'customer_company' ) );
			} else {
				$username = esc_html__( 'Guest', 'awebooking' );
			}

			/* translators: 1 Booking ID, 2 by user */
			printf( esc_html__( '%1$s by %2$s', 'awebooking' ),
				'<a href="' . esc_url( admin_url( 'post.php?post=' . absint( $the_booking['id'] ) . '&action=edit' ) ) . '" class="row-title"><strong>#' . esc_html( $the_booking->get_booking_number() ) . '</strong></a>',
				wp_kses_post( $username )
			);
		}

		if ( $customer_note = $the_booking['customer_note'] ) : ?>
			<span class="tippy abrs-fright" data-tippy-interactive="true" data-tippy-html="#private_customer_note_<?php echo esc_attr( $the_booking['id'] ); ?>">
				<span class="screen-reader-text"><?php esc_html_e( 'Customer note', 'awebooking' ); ?></span>
				<span class="dashicons dashicons-admin-comments"></span>
			</span>

			<div id="private_customer_note_<?php echo esc_attr( $the_booking['id'] ); ?>" style="display: none;">
				<div class="abrs-tooltip-note"><?php echo wp_kses_post( wptexturize( wpautop( $customer_note ) ) ); ?></div>
			</div>
		<?php endif; // @codingStandardsIgnoreLine

		echo '<button type="button" class="toggle-row"><span class="screen-reader-text">' . esc_html__( 'Show more details', 'awebooking' ) . '</span></button>';
	}

	/**
	 * Display columm: booking_status.
	 *
	 * @return void
	 */
	protected function display_booking_status_column() {
		$status = $this->booking->get( 'status' );

		printf( '<mark class="booking-status abrs-label %s"><span>%s</span></mark>', esc_attr( sanitize_html_class( $status . '-color' ) ), esc_html( abrs_get_booking_status_name( $status ) ) );
	}

	/**
	 * Display column: nights stay.
	 *
	 * @return void
	 */
	protected function display_booking_nights_column() {
		$nights_stay = $this->booking->get( 'nights_stay' );

		?>

		<?php if ( - 1 === $nights_stay ) : ?>
			<span title="<?php esc_attr_e( 'Length of stay varies, see each room.', 'awebooking' ); ?>">
				<span class="dashicons dashicons-info"></span>
			</span>
		<?php else : ?>
			<?php echo absint( $nights_stay ); ?>
		<?php endif;
	}

	/**
	 * Display column: check-in.
	 *
	 * @return void
	 */
	protected function display_booking_check_in_column() {
		echo esc_html( abrs_format_date( $this->booking->get_check_in_date() ) );
	}

	/**
	 * Display column: check-out.
	 *
	 * @return void
	 */
	protected function display_booking_check_out_column() {
		echo esc_html( abrs_format_date( $this->booking->get_check_out_date() ) );
	}

	/**
	 * Display columm: booking_date.
	 *
	 * @return void
	 */
	protected function display_booking_date_column() {
		$date_created = abrs_date_time( $this->booking->get( 'date_created' ) );

		if ( is_null( $date_created ) ) {
			return;
		}

		// Check if the booking was created within the last 24 hours, and not in the future.
		// We will show the date as human readable date time by using human_time_diff.
		if ( ! $date_created->isFuture() && $date_created->gt( abrs_date_time( 'now' )->subDay() ) ) {
			/* translators: %s: human-readable time difference */
			$show_date = sprintf( _x( '%s ago', '%s = human-readable time difference', 'awebooking' ),
				human_time_diff( $date_created->getTimestamp(), current_time( 'timestamp', true ) )
			);
		} else {
			$show_date = abrs_format_date( $date_created );
		}

		printf(
			'<abbr datetime="%1$s" title="%2$s">%3$s</abbr>',
			esc_attr( $date_created->toDateTimeString() ),
			esc_html( abrs_format_date_time( $date_created ) ),
			esc_html( $show_date )
		);
	}

	/**
	 * Display columm: booking_total.
	 *
	 * @return void
	 */
	protected function display_booking_total_column() {
		// @codingStandardsIgnoreLine
		echo '<span class="abrs-badge">' . abrs_format_price( $this->booking->get( 'total' ), $this->booking->get( 'currency' ) ) . '</span>';
	}

	/**
	 * Display columm: booking_paid.
	 *
	 * @return void
	 */
	protected function display_booking_paid_column() {
		global $the_booking;
		?>
			<span class="tippy" data-tippy-interactive="true" data-tippy-html="#private_balance_due_<?php echo esc_attr( $the_booking['id'] ); ?>">
			<span class="abrs-badge"><?php echo abrs_format_price( $this->booking->get( 'paid' ), $this->booking->get( 'currency' ) ); ?></span>
			</span>

			<div id="private_balance_due_<?php echo esc_attr( $the_booking['id'] ); ?>" style="display: none;">
				<div class="abrs-tooltip-booking-paid-column">
					<?php esc_html_e( 'Balance due: ', 'awebooking' ); ?>
					<?php echo abrs_format_price( $this->booking->get( 'balance_due' ), $this->booking->get( 'currency' ) ); ?>
				</div>
			</div>
		<?php

	}

	/**
	 * Display columm: booking_summary.
	 *
	 * @return void
	 */
	protected function display_booking_summary_column() {
		$booked_rooms = $this->booking->get_rooms();

		if ( abrs_blank( $booked_rooms ) ) : ?>

			<?php esc_html_e( 'No rooms found', 'awebooking' ); ?>

		<?php else : ?>

			<?php
			$first_room = $booked_rooms->first();
			$rooms_left = absint( count( $booked_rooms ) - 1 );

			$timespan = abrs_optional( $first_room->get_timespan() );

			$nights = sprintf(
				'&comma; <span class="">%1$d %2$s</span>',
				$timespan->get_nights(),
				_n( 'night', 'nights', $timespan->get_nights(), 'awebooking' )
			);

			$guest = $first_room->get_guests();

			$more = '';
			if ( $rooms_left ) {
				$more = sprintf(
					/* translators: 1: number of rooms */
					_nx(
						'&comma; more %1$s room',
						'&comma; more %1$s rooms',
						$rooms_left,
						'rooms left',
						'awebooking'
					),
					number_format_i18n( $rooms_left )
				);
			}

			printf( esc_html__( '%1$s%2$s%3$s%4$s ', 'awebooking' ), esc_html( $first_room->get_name() ), $nights, $guest->as_string(), $more );
		endif;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function render_filters() {
		$user_id = '';
		$user_string = '';

		if ( ! empty( $_GET['_customer'] ) ) {
			$user = get_user_by( 'id', absint( $_GET['_customer'] ) );

			/* translators: 1: user display name 2: user ID 3: user email */
			$user_string = sprintf( esc_html__( '%1$s (#%2$s - %3$s)', 'awebooking' ), $user->display_name, $user->ID, $user->user_email );
			$user_id = $user->ID;
		}

		?><select class="awebooking-search-customer" name="_customer" data-placeholder="<?php esc_attr_e( 'Query for a customer&hellip;', 'awebooking' ); ?>">
			<option value="<?php echo esc_attr( $user_id ); ?>" selected="selected"><?php echo wp_kses_post( $user_string ); ?><option>
		</select><?php // @codingStandardsIgnoreLine
	}

	/**
	 * {@inheritdoc}
	 */
	protected function query_filters( $query_vars ) {
		global $wp_post_statuses;

		// Handler the sorting.
		if ( isset( $query_vars['orderby'] ) ) {
			switch ( $query_vars['orderby'] ) {
				case 'booking_total':
					$query_vars = array_merge( $query_vars, [
						'meta_key'  => '_total',
						'orderby'   => 'meta_value_num',
					]);
					break;
			}
		}

		// Filter the bookings by the posted customer.
		if ( ! empty( $_GET['_customer'] ) ) {
			$query_vars['meta_query'] = [
				[
					'key'     => '_customer_id',
					'value'   => absint( $_GET['_customer'] ),
					'compare' => '=',
				],
			];
		}

		// Filter the bookings by specified room ID.
		if ( ! empty( $_GET['_room'] ) ) {
			$room_id = absint( $_GET['_room'] );

			$bookings = abrs_get_booking_booked_by_room( $room_id );

			$query_vars['post__in'] = array_merge( (array) $bookings, [ 0 ] );
		}

		// Merge booking statuses on "All".
		if ( empty( $query_vars['post_status'] ) ) {
			$statuses = abrs_get_booking_statuses();
			unset( $statuses['awebooking-cancelled'] );

			foreach ( $statuses as $status => $value ) {
				if ( isset( $wp_post_statuses[ $status ] ) && false === $wp_post_statuses[ $status ]->show_in_admin_all_list ) {
					unset( $statuses[ $status ] );
				}
			}

			$query_vars['post_status'] = array_keys( $statuses );
		}

		return $query_vars;
	}

	/**
	 * Correct the label when searching bookings.
	 *
	 * @param  mixed $query Current search query.
	 * @return string
	 */
	public function correct_search_label( $query ) {
		global $pagenow, $typenow;

		if ( 'edit.php' !== $pagenow || 'awebooking' !== $typenow
			|| ! get_query_var( 'perform_booking_search' )
			|| ! isset( $_GET['s'] ) ) {
			return $query;
		}

		return abrs_clean( wp_unslash( $_GET['s'] ) ); // WPCS: input var ok, sanitization ok.
	}

	/**
	 * Query custom fields as well as content.
	 *
	 * @param \WP_Query $wp The WP_Query object.
	 *
	 * @access private
	 */
	public function search_custom_fields( $wp ) {
		global $pagenow;

		if ( 'edit.php' !== $pagenow
			|| empty( $wp->query_vars['s'] )
			|| 'awebooking' !== $wp->query_vars['post_type']
			|| ! isset( $_GET['s'] ) ) {
			return;
		}

		$post_ids = abrs_search_booking_by_term( abrs_clean( wp_unslash( $_GET['s'] ) ) ); // WPCS: input var ok, sanitization ok.

		if ( ! empty( $post_ids ) ) {
			// Remove "s" - we don't want to search order name.
			unset( $wp->query_vars['s'] );

			$wp->query_vars['perform_booking_search'] = true;

			// Query by found posts.
			$wp->query_vars['post__in'] = array_merge( $post_ids, [ 0 ] );
		}
	}
}
