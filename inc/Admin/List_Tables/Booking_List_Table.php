<?php
namespace AweBooking\Admin\List_Tables;

use AweBooking\Constants;
use AweBooking\AweBooking;
use AweBooking\Booking\Booking;

class Booking_List_Table extends Post_Type_Abstract {
	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	protected $post_type = AweBooking::BOOKING;

	/**
	 * List table primary column.
	 *
	 * @var string
	 */
	protected $primary_column = 'booking_title';

	/**
	 * Init somethings hooks.
	 *
	 * @access private
	 */
	public function init() {
		add_filter( 'request', [ $this, 'request_query' ] );
		add_filter( 'post_row_actions', [ $this, 'disable_quick_edit' ], 10, 2 );
		add_filter( 'parse_query', [ $this, 'perform_searching' ] );
		add_action( 'restrict_manage_posts', [ $this, 'restrict_manage_posts' ] );
	}

	public function perform_searching( $wp ) {
		global $pagenow;

		if ( 'edit.php' !== $pagenow || empty( $wp->query_vars['s'] )
			|| Constants::BOOKING !== $wp->query_vars['post_type'] ) {
			return;
		}

		$post_ids = $this->search_bookings( $wp->query_vars['s'] );
		if ( ! empty( $post_ids ) ) {
			// Remove "s" - we don't want to search order name.
			unset( $wp->query_vars['s'] );

			// Search by found posts.
			$wp->query_vars['post__in'] = array_merge( $post_ids, array( 0 ) );
		}
	}

	/**
	 * Search order data for a term and return ids.
	 *
	 * @param  string $term
	 * @return array of ids
	 */
	public function search_bookings( $term ) {
		global $wpdb;

		$search_fields = array_map( [ $this, 'awebooking_clean' ], apply_filters( 'awebooking/booking_search_fields', array(
			'_customer_first_name',
			'_customer_last_name',
			'_customer_address',
			'_customer_company',
			'_customer_email',
			'_customer_phone',
		) ) );

		$booking_ids = array();

		if ( is_numeric( $term ) ) {
			$booking_ids[] = absint( $term );
		}

		if ( ! empty( $search_fields ) ) {
			$booking_ids = array_unique( array_merge(
				$booking_ids,
				$wpdb->get_col(
					$wpdb->prepare( "SELECT DISTINCT p1.post_id FROM {$wpdb->postmeta} p1 WHERE p1.meta_value LIKE '%%%s%%'", $wpdb->esc_like( $this->awebooking_clean( $term ) ) ) . " AND p1.meta_key IN ('" . implode( "','", array_map( 'esc_sql', $search_fields ) ) . "')"
				),
				$wpdb->get_col(
					$wpdb->prepare( "
						SELECT booking_id
						FROM {$wpdb->prefix}awebooking_booking_items as booking_items
						WHERE booking_item_name LIKE '%%%s%%'
						",
						$wpdb->esc_like( $this->awebooking_clean( $term ) )
					)
				)
			) );
		}

		return apply_filters( 'awebooking/booking_search_results', $booking_ids, $term, $search_fields );
	}

	/**
	 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
	 * Non-scalar values are ignored.
	 * @param string|array $var
	 * @return string|array
	 */
	public function awebooking_clean( $var ) {
		if ( is_array( $var ) ) {
			return array_map( 'awebooking_clean', $var );
		} else {
			return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
		}
	}

	/**
	 * Filters for post types.
	 *
	 * @return void
	 */
	public function restrict_manage_posts() {
		global $typenow;

		if ( Constants::BOOKING !== $typenow ) {
			return;
		}

		$this->booking_filters();
	}

	/**
	 * Show custom filters to filter orders by status/customer.
	 *
	 * @return void
	 */
	protected function booking_filters() {
		$user_string = '';
		$user_id     = '';
		if ( ! empty( $_GET['customer'] ) ) {
			$user_id     = absint( $_GET['customer'] );
			$user        = get_user_by( 'id', $user_id );
			/* translators: 1: user display name 2: user ID 3: user email */
			$user_string = sprintf(
				esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'awebooking' ),
				$user->display_name,
				absint( $user->ID ),
				$user->user_email
			);
		}
		?>
		<select class="awebooking-customer-search" name="customer" data-placeholder="<?php esc_attr_e( 'Search for a customer&hellip;', 'awebooking' ); ?>" data-allow-clear="true" style="width: 200px;">
			<option value="<?php echo esc_attr( $user_id ); ?>" selected="selected"><?php echo htmlspecialchars( $user_string ); ?><option>
		</select>
		<?php
	}

	/**
	 * Registers admin columns to display.
	 *
	 * @access private
	 *
	 * @param  array $columns Array of registered column names/labels.
	 * @return array
	 */
	public function columns( $columns ) {
		return [
			'cb'                => '<input type="checkbox" />',
			'booking_status'    => esc_html__( 'Status', 'awebooking' ),
			'booking_title'     => esc_html__( 'Booking', 'awebooking' ),
			// 'booking_room'      => esc_html__( 'Room', 'awebooking' ),
			// 'check_in_out_date' => esc_html__( 'Check-in / Check-out', 'awebooking' ),
			// 'booking_guests'    => esc_html__( 'Guests', 'awebooking' ),
			'booking_total'     => esc_html__( 'Total', 'awebooking' ),
			'booking_date'      => esc_html__( 'Date', 'awebooking' ),
		];
	}

	/**
	 * Registers which columns are sortable.
	 *
	 * @access private
	 *
	 * @param  array $sortable_columns Array of registered column keys => data-identifier.
	 * @return array
	 */
	public function sortable_columns( $sortable_columns ) {
		$sortable_columns['booking_title'] = 'ID';
		$sortable_columns['booking_date']  = 'date';
		$sortable_columns['booking_total'] = 'booking_total';

		return $sortable_columns;
	}

	/**
	 * Handles admin column display.
	 *
	 * @access private
	 *
	 * @param string $column  The name of the column to display.
	 * @param int    $post_id Current post ID.
	 */
	public function columns_display( $column, $post_id ) {
		global $the_booking, $post;

		switch ( $column ) {
			case 'booking_status':
				$status = $the_booking->get_status();
				$statuses = awebooking( 'setting' )->get_booking_statuses();

				$status_color = '';
				$status_label = isset( $statuses[ $status ] ) ? $statuses[ $status ] : '';

				switch ( $status ) {
					case Booking::PENDING:
						$status_color = 'awebooking-label--info';
						break;
					case Booking::PROCESSING:
						$status_color = 'awebooking-label--warning';
						break;
					case Booking::CANCELLED:
						$status_color = 'awebooking-label--danger';
						break;
					case Booking::COMPLETED:
						$status_color = 'awebooking-label--success';
						break;
				}

				if ( $the_booking->is_featured() ) {
					echo '<span class="dashicons dashicons-star-filled"></span>';
				}

				printf( '<span class="awebooking-label %2$s">%1$s</span>', $status_label, $status_color );
			break;

			case 'booking_title':
				$this->print_booking_title( $the_booking );
				echo '<button type="button" class="toggle-row"><span class="screen-reader-text">' . esc_html__( 'Show more details', 'awebooking' ) . '</span></button>';
				break;

			case 'booking_date':
				printf( '<abbr title="%s">%s</abbr>',
					esc_attr( $the_booking->get_booking_date()->toDateTimeString() ),
					esc_html( $the_booking->get_booking_date() )
				);
				break;

			case 'booking_guestss':
				printf( '<span class="">%1$d %2$s</span>',
					$the_booking->get_adults(),
					_n( 'adult', 'adults', $the_booking->get_adults(), 'awebooking' )
				);

				if ( $the_booking['children'] ) {
					printf( ' &amp; <span class="">%1$d %2$s</span>',
						$the_booking->get_children(),
						_n( 'child', 'children', $the_booking->get_children(), 'awebooking' )
					);
				}

				if ( $the_booking['infants'] ) {
					printf( ' &amp; <span class="">%1$d %2$s</span>',
						$the_booking->get_infants(),
						_n( 'infant', 'infants', $the_booking->get_infants(), 'awebooking' )
					);
				}
				break;

			case 'check_in_out_date':
				if ( $the_booking->get_arrival_date() ) {
					printf( '<strong></strong> <br> <span>%s</span> - <span>%s</span>',
						// $date_period->nights(),
						// _n( 'night', 'nights', $date_period->nights(), 'awebooking' ),
						$the_booking->get_arrival_date()->toDateString(),
						$the_booking->get_departure_date()->toDateString()
					);
				}
				break;

			case 'booking_total' :
				printf( '<span class="awebooking-label %2$s">%1$s</span>',
					$the_booking->get_total(),
					$the_booking->get_total()->is_zero() ? 'awebooking-label--danger' : 'awebooking-label--info'
				);

				if ( $the_booking['payment_method_title'] ) {
					echo '<small class="meta">' . esc_html__( 'Via', 'awebooking' ) . ' ' . esc_html( $the_booking->get_payment_method_title() ) . '</small>';
				}
			break;

			case 'booking_rooms':
				$the_room = $the_booking->get_room_unit();

				if ( is_null( $the_room ) ) {
					printf( '<span class="awebooking-invalid">%s</span>', esc_html__( 'Room was not available by time selected', 'awebooking' ) );
				} elseif ( $the_room->exists() ) {
					$room_type = $the_room->get_room_type();
					$hotel_location = $room_type->get_location();

					printf(
						'<a href="%1$s" target="_blank"><b>%2$s</b></a> (%3$s)',
						esc_url( get_edit_post_link( $room_type->get_id() ) ),
						esc_html( $room_type->get_title() ),
						esc_html( $the_room->get_name() )
					);

					if ( awebooking()->is_multi_location() && $hotel_location ) {
						echo '<br>' . esc_html__( 'Location:', 'awebooking' ) . ' <span>' . esc_html( $hotel_location->name ) . '</span>';
					}
				} else {
					echo '<span class="awebooking-invalid">' . esc_html__( 'Room was deleted', ' awebooking' ) . '</span>';
				}
				break;
		} // End switch().
	}

	/**
	 * Print the booking title.
	 *
	 * @param  Booking $booking //.
	 * @return void
	 */
	protected function print_booking_title( Booking $booking ) {
		if ( $booking['customer_id'] ) {
			$userdata = get_userdata( $booking['customer_id'] );
			$username = $userdata ? sprintf( '<a href="user-edit.php?user_id=%d">%s</a>', absint( $booking['customer_id'] ), esc_html( $userdata->display_name ) ) : '';
		} elseif ( $customber_name = $booking->get_customer_name() ) {
			$username = $customber_name;
		} elseif ( $booking['customer_company'] ) {
			$username = trim( $booking->get_customer_company() );
		} else {
			$username = esc_html__( 'Guest', 'awebooking' );
		}

		printf( esc_html__( '%1$s by %2$s', 'awebooking' ),
			'<a href="' . admin_url( 'post.php?post=' . absint( $booking['id'] ) . '&action=edit' ) . '" class="row-title"><strong>#' . esc_attr( $booking->get_id() ) . '</strong></a>',
			$username
		);

		if ( $booking['customer_email'] ) {
			echo '<small class="meta email"><a href="' . esc_url( 'mailto:' . $booking->get_customer_email() ) . '">' . esc_html( $booking->get_customer_email() ) . '</a></small>';
		}
	}

	/**
	 * Remove the "Quick Edit" link.
	 *
	 * @param array   $actions An array of row action links.
	 * @param WP_Post $post    The post object.
	 */
	public function disable_quick_edit( $actions, $post ) {
		// Prevent if not in booking post type.
		if ( get_post_type( $post ) !== $this->post_type ) {
			return $actions;
		}

		// Remove the "Quick Edit" link.
		if ( isset( $actions['inline hide-if-no-js'] ) ) {
			unset( $actions['inline hide-if-no-js'] );
		}

		return $actions;
	}

	/**
	 * Filters and sorting handler.
	 *
	 * @param  array $query_vars WP query_vars property.
	 * @return array
	 */
	public function request_query( $query_vars ) {
		global $typenow, $wp_query, $wp_post_statuses;

		// Prevent actions if not current post type.
		if ( $this->post_type !== $typenow ) {
			return $query_vars;
		}

		// Filter the orders by the posted customer.
		if ( isset( $_GET['customer'] ) && $_GET['customer'] > 0 ) {
			$query_vars['meta_query'] = [[
				'key'     => '_customer_id',
				'value'   => absint( $_GET['customer'] ),
				'compare' => '=',
			]];
		}

		// Sorting handler.
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

		// Added booking status only in "All" section in list-table.
		if ( ! isset( $query_vars['post_status'] ) ) {
			$statuses = awebooking( 'setting' )->get_booking_statuses();

			foreach ( $statuses as $status => $display_name ) {
				if ( isset( $wp_post_statuses[ $status ] ) && false === $wp_post_statuses[ $status ]->show_in_admin_all_list ) {
					unset( $statuses[ $status ] );
				}
			}

			$query_vars['post_status'] = array_keys( $statuses );
		}

		return $query_vars;
	}
}
