<?php
namespace AweBooking\Admin\List_Tables;

use AweBooking\Constants;

class Booking_List_Table extends Abstract_List_Table {
	/**
	 * The post type name.
	 *
	 * @var string
	 */
	protected $list_table = Constants::BOOKING;

	/**
	 * {@inheritdoc}
	 */
	protected function get_row_actions( $actions, $post ) {
		return []; // No row actions.
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

		?><select class="awebooking-search-customer" name="_customer" data-placeholder="<?php esc_attr_e( 'Search for a customer&hellip;', 'awebooking' ); ?>">
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

		// Merge booking statuses on "All".
		if ( ! isset( $query_vars['post_status'] ) ) {
			$post_statuses = abrs_list_booking_statuses();

			foreach ( $post_statuses as $status => $value ) {
				if ( isset( $wp_post_statuses[ $status ] ) && false === $wp_post_statuses[ $status ]->show_in_admin_all_list ) {
					unset( $post_statuses[ $status ] );
				}
			}

			$query_vars['post_status'] = array_keys( $post_statuses );
		}

		return $query_vars;
	}
}
