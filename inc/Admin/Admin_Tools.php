<?php
namespace AweBooking\Admin;

use AweBooking\Roles;

class Admin_Tools {
	/**
	 * Run a task.
	 *
	 * @param  string $task The task name.
	 * @return array
	 */
	public function run( $task ) {
		$message = '';

		switch ( $task ) {
			case 'clear_expired_transients':
				/* translators: %d: amount of expired transients */
				$message = sprintf( esc_html__( '%d transients rows cleared', 'awebooking' ), $this->clear_expired_transients() );
				break;

			case 'optimize_database':
				$this->optimize_database();
				$message = 'Optimized database!';
				break;

			case 'reset_roles':
				$this->reset_roles();
				$message = 'Roles successfully reset';
				break;
		}

		return compact( 'message' );
	}

	/**
	 * Clear expired transients.
	 *
	 * @return int
	 */
	public function clear_expired_transients() {
		delete_transient( 'awebooking_premium_themes' );
		delete_transient( 'awebooking_premium_addons' );

		return abrs_delete_expired_transients();
	}

	/**
	 * Delete all orphan rows in AweBooking tables.
	 *
	 * @return void
	 */
	public function optimize_database() {
		global $wpdb;

		// Delete orphaned data. @codingStandardsIgnoreStart
		$wpdb->query( "DELETE t1 FROM {$wpdb->prefix}awebooking_rooms AS t1 LEFT JOIN {$wpdb->prefix}posts AS t2 ON t2.ID = t1.id WHERE t2.ID IS NULL" );
		$wpdb->query( "DELETE t1 FROM {$wpdb->prefix}awebooking_pricing AS t1 LEFT JOIN {$wpdb->prefix}posts AS t2 ON t2.ID = t1.rate_id WHERE t2.ID IS NULL" );
		$wpdb->query( "DELETE t1 FROM {$wpdb->prefix}awebooking_booking AS t1 LEFT JOIN {$wpdb->prefix}awebooking_rooms AS t2 ON t2.id = t1.room_id WHERE t2.id IS NULL" );
		$wpdb->query( "DELETE t1 FROM {$wpdb->prefix}awebooking_availability AS t1 LEFT JOIN {$wpdb->prefix}awebooking_rooms AS t2 ON t2.id = t1.room_id WHERE t2.id IS NULL" );
		$wpdb->query( "DELETE t1 FROM {$wpdb->prefix}awebooking_booking_items AS t1 LEFT JOIN {$wpdb->prefix}posts AS t2 ON t2.ID = t1.booking_id WHERE t2.ID IS NULL" );
		$wpdb->query( "DELETE t1 FROM {$wpdb->prefix}awebooking_booking_itemmeta AS t1 LEFT JOIN {$wpdb->prefix}awebooking_booking_items AS t2 ON t2.booking_item_id = t1.booking_item_id WHERE t2.booking_item_id IS NULL" );

		// Delete all rows don't have any data.
		$sum_days = 'd1 + d2 + d3 + d4 + d5 + d6 + d7 + d8 + d9 + d10 + d11 + d12 + d13 + d14 + d15 + d16 + d17 + d18 + d19 + d20 + d21 + d22 + d23 + d24 + d25 + d26 + d27 + d28 + d29 + d30 + d31';
		$wpdb->query( "DELETE FROM {$wpdb->prefix}awebooking_booking WHERE ({$sum_days}) = 0" );
		$wpdb->query( "DELETE FROM {$wpdb->prefix}awebooking_pricing WHERE ({$sum_days}) = 0" );
		$wpdb->query( "DELETE FROM {$wpdb->prefix}awebooking_availability WHERE ({$sum_days}) = 0" );
		// @codingStandardsIgnoreEnd
	}

	/**
	 * Reset capabilities.
	 *
	 * @return void
	 */
	public function reset_roles() {
		$roles = new Roles;

		// Remove then re-add caps and roles.
		$roles->remove();
		$roles->create();
	}

	/**
	 * A list of available tools for use in the system status section.
	 *
	 * @return array
	 */
	public function get_tools() {
		return apply_filters( 'abrs_debug_tools', [
			'clear_expired_transients' => [
				'name'   => esc_html__( 'Expired transients', 'awebooking' ),
				'button' => esc_html__( 'Clear transients', 'awebooking' ),
				'desc'   => esc_html__( 'This tool will clear ALL expired transients from WordPress.', 'awebooking' ),
			],
			'optimize_database'        => [
				'name'   => esc_html__( 'Optimize database', 'awebooking' ),
				'button' => esc_html__( 'Optimize', 'awebooking' ),
				'desc'   => esc_html__( 'This tool will delete all orphan rows in AweBooking tables.', 'awebooking' ),
			],
			'reset_roles' => array(
				'name'   => esc_html__( 'Capabilities', 'awebooking' ),
				'button' => esc_html__( 'Reset capabilities', 'awebooking' ),
				'desc'   => esc_html__( 'This tool will reset the admin, customer, receptionist and hotel manager roles to default.', 'awebooking' ),
			),
		]);
	}
}
