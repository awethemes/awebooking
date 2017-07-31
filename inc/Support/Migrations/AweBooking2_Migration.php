<?php
namespace AweBooking\Migrations;

use Carbon\Carbon;
use AweBooking\Booking;
use AweBooking\Room_State;
use AweBooking\Support\Formatting;

class AweBooking2_Migration {
	/**
	 * Mapping state of old and new AweBooking.
	 *
	 * @var array
	 */
	protected $mapping_states = [
		0 => Room_State::BOOKED,      // Completed
		1 => Room_State::UNAVAILABLE, // Unavailable
		2 => Room_State::AVAILABLE,   // Available
		3 => Room_State::PENDING,     // Pending.
	];

	/**
	 * Mapping booking status of old and new AweBooking.
	 *
	 * @var array
	 */
	protected $mapping_status = [
		'apb-pending'    => Booking::PENDING,
		'apb-completed'  => Booking::COMPLETED,
		'apb-cancelled'  => Booking::CANCELLED,
	];

	/**
	 * Mapping 'old_room' => 'new_room' ID.
	 *
	 * @var array
	 */
	protected $mapping_rooms = [];

	/**
	 * Migration constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_menu_page' ] );
		add_action( 'admin_notices', [ $this, 'notice_migration' ] );
	}

	/**
	 * Register the admin menu.
	 *
	 * @return void
	 */
	public function register_menu_page() {
		add_management_page( 'AweBooking Migration', 'AweBooking Migration', 'manage_options', 'awebooking-migrate', array( $this, 'output' ) );
	}

	/**
	 * Add notice migration to user.
	 *
	 * @return void
	 */
	public function notice_migration() {
		$screen = get_current_screen();

		if ( 'tools_page_awebooking-migrate' === $screen->id ) {
			return;
		}

		if ( ! $this->exists_old_version() ) {
			return;
		}

		?><div class="notice notice-warning is-dismissible">
			<p><strong>AweBooking Migration</strong> – Seem you just upgraded from AweBooking v2.x.</p>
			<p>This version not compatible with old version, so if you need migrate database from old version <a href="<?php echo esc_url( admin_url( 'tools.php?page=awebooking-migrate' ) ); ?>">click here to run it now.</a></p>
		</div><?php
	}

	/**
	 * Output the migrate interface.
	 *
	 * @return void
	 */
	public function output() {
		?><div class="wrap">
			<h1><?php echo esc_html__( 'Awebooking: Migration', 'awebooking' ) ?></h1>

			<style type="text/css">
				.awebooking-log { font-size: 12px; margin-top: 0; margin-bottom: 5px; }
				.awebooking-log-info { color: #00bcd4; }
				.awebooking-log-error { color: #ff5722; }
				.awebooking-log-success { color: #4caf50; }
				.awebooking-log-warning { color: #ff9800; }
			</style>

			<?php if ( $this->exists_old_version() ) : ?>

				<div class="awebooking-migrate-notice">
					<p>We need to update your store database to the latest version.</p>
					<p>Some data we can't migrate because difference between two version:</p>

					<ol>
						<li><strong>Child capacity</strong> and <strong>Sleeping capacity</strong> in room-type.</li>
						<li><strong>Discount</strong> in room-type.</li>
						<li><strong>Extra Price</strong> in room-type.</li>
						<li><strong>Extra Description</strong> in room-type.</li>
						<li>Something else...</li>
					</ol>

					<p><a href="tools.php?page=awebooking-migrate&run-migrate=1" class="button button-primary">Run migration</a></p>
				</div>

				<div class="awebooking-migrate-box">
					<?php if ( isset( $_REQUEST['run-migrate'] ) && $_REQUEST['run-migrate'] ) :
						@$this->migration(); // Prevent all errors.
					endif ?>
				</div>

			<?php else : ?>

				<p>You're using latest version of AweBooking, so nothing to do.</p>

			<?php endif ?>

		</div><?php
	}

	/**
	 * If old version exists.
	 *
	 * @return bool
	 */
	public function exists_old_version() {
		global $wpdb;

		return (
			0 !== $wpdb->query( "SHOW TABLES LIKE '{$wpdb->prefix}apb_pricing'" ) &&
			0 !== $wpdb->query( "SHOW TABLES LIKE '{$wpdb->prefix}apb_availability'" )
		);
	}

	/**
	 * Write message log.
	 *
	 * @param  string $message Message log.
	 * @param  string $level   Message level "debug", "info", "error", "success", "warning".
	 * @return void
	 */
	protected function write_log( $message, $level = 'debug' ) {
		printf( '<p class="awebooking-log awebooking-log-%s">%s</p>', esc_attr( $level ), esc_html( $message ) );
	}

	/**
	 * Run the migration.
	 *
	 * @return void
	 */
	public function migration() {
		try {
			// Do not change order of this task.
			$this->doing_migrate_rooms();
			$this->doing_migrate_services();

			$this->doing_migrate_pricing();
			$this->doing_migrate_availability();

			$this->doing_migrate_options();
			$this->doing_migrate_bookings();
		} catch ( \Exception $e ) {
			$this->write_log( $e->getMessage(), 'error' );
		}

		@flush_rewrite_rules();

		// Completed message.
		echo '<p>AweBooking data update complete. Thank you for updating to the latest version!</p>';
	}

	/**
	 * Doing migrate rooms.
	 *
	 * @return void
	 */
	protected function doing_migrate_rooms() {
		global $wpdb;

		$this->write_log( esc_html__( '* Run migrate room-type and rooms...', 'awebooking' ), 'info' );

		// Get old rooms type.
		$old_rooms = get_posts([
			'post_type'   => 'apb_room_type',
			'numberposts' => -1,
		]);

		foreach ( $old_rooms as $old_room ) {
			$working_id = $old_room->ID;

			$this->write_log( sprintf( esc_html__( 'Migrating room-type #%s', 'awebooking' ), $working_id ), 'debug' );

			// First, set room_type post type.
			set_post_type( $working_id, 'room_type' );

			$old_single_rooms = get_children([
				'post_parent' => $working_id,
				'post_type'   => 'apb_room',
				'numberposts' => -1,
			]);

			// Update number of rooms.
			// We will cache mapping_rooms, so after that pricing can known.
			$this->mapping_rooms = [];
			foreach ( $old_single_rooms as $old_single_room ) {
				$results = awebooking( 'store.room' )->insert( $working_id, $old_single_room->post_title );

				if ( $results ) {
					$insert_id = $wpdb->insert_id;
					$this->mapping_rooms[ $old_single_room->ID ] = $insert_id;

					$this->write_log( sprintf( esc_html__( 'Migrated room "%s", mapping new ID #%s', 'awebooking' ), $old_single_room->post_title, $insert_id ), 'debug' );
				} else {
					$this->write_log( sprintf( esc_html__( 'Migrate room "%s" has failed', 'awebooking' ), $old_single_room->post_title ), 'warning' );
				}
			}

			// Now we'll touch metadata.
			$metadata = get_post_meta( $working_id );
			foreach ( $metadata as $meta_key => &$meta_value ) {
				$meta_value = ( 1 === count( $meta_value ) ) ? maybe_unserialize( $meta_value[0] ) : array_map( 'maybe_unserialize', $meta_value );
			}

			// Update room type metadata.
			update_post_meta( $working_id, 'minimum_night', 1 );
			update_post_meta( $working_id, 'number_children', 0 );
			update_post_meta( $working_id, 'max_adults', 0 );
			update_post_meta( $working_id, 'max_children', 0 );

			delete_post_meta( $working_id, 'min_children' );
			delete_post_meta( $working_id, 'max_children' );

			if ( isset( $metadata['base_price'] ) ) {
				update_post_meta( $working_id, 'base_price', (float) $metadata['base_price'] );
			}

			if ( isset( $metadata['min_night'] ) ) {
				$min_night = ( (int) $metadata['min_night'] > 0 ) ? (int) $metadata['min_night'] : 1;
				update_post_meta( $working_id, 'minimum_night', $min_night );

				delete_post_meta( $working_id, 'min_night' );
			}

			if ( isset( $metadata['min_sleeps'] ) && isset( $metadata['max_sleeps'] ) ) {
				$max_adults = (int) $metadata['max_sleeps'] - (int) $metadata['min_sleeps'];
				update_post_meta( $working_id, 'max_adults', ( $max_adults > 1 ) ? $max_adults : 0 );

				delete_post_meta( $working_id, 'min_sleeps' );
				delete_post_meta( $working_id, 'max_sleeps' );
			}

			if ( isset( $metadata['base_price_for'] ) ) {
				update_post_meta( $working_id, 'number_adults', (int) $metadata['base_price_for'] );

				delete_post_meta( $working_id, 'base_price_for' );
			}

			if ( isset( $metadata['room_desc'] ) ) {
				update_post_meta( $working_id, 'short_description', $metadata['room_desc'] );

				delete_post_meta( $working_id, 'room_desc' );
			}

			if ( isset( $metadata['apb_gallery'] ) && is_array( $metadata['apb_gallery'] ) ) {
				$gallery_ids = array_filter( $metadata['apb_gallery'], function( $id ) {
					return is_numeric( $id );
				});

				$gallery = [];
				if ( ! empty( $gallery_ids ) ) {
					foreach ( $gallery_ids as $id ) {
						$gallery[ $id ] = wp_get_attachment_image_url( $id, 'full' );
					}

					update_post_meta( $working_id, 'gallery', $gallery );
				}

				delete_post_meta( $working_id, 'apb_gallery' );
			}

			$this->write_log( sprintf( esc_html__( 'Migrating room-type #%s successfully!', 'awebooking' ), $working_id ), 'success' );
		} // End foreach().
	}

	/**
	 * Doing migrate services table.
	 *
	 * @return void
	 */
	protected function doing_migrate_services() {
		global $wpdb;

		$have_table = $wpdb->query( "SHOW TABLES LIKE '{$wpdb->prefix}apb_booking_options'" );
		if ( 0 === $have_table ) {
			return;
		}

		$this->write_log( esc_html__( '* Migrating services...', 'awebooking' ), 'info' );

		$old_services = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}apb_booking_options WHERE entity_type = 'apb_room_type'", ARRAY_A );
		foreach ( $old_services as $old_service ) {
			if ( empty( $old_service['option_name'] ) || empty( $old_service['object_id'] ) ) {
				continue;
			}

			// First, insert extra service.
			$insert_term = wp_insert_term( $old_service['option_name'], 'hotel_extra_service', array(
				'description' => $old_service['option_desc'],
			));

			$this->write_log( sprintf( esc_html__( 'Migrating service "%s"', 'awebooking' ), $old_service['option_name'] ), 'debug' );

			// If not see the error, we'll continue update the metadata.
			if ( ! is_wp_error( $insert_term ) ) {
				update_term_meta( $insert_term['term_id'], 'price', (float) $old_service['option_value'] );

				if ( 'add' !== $old_service['option_operation'] ) {
					update_term_meta( $insert_term['term_id'], 'operation', 'add-daily' );
				}

				// Finally, mapping created extra service to room type.
				$post = get_post( $old_service['object_id'] );
				if ( ! is_null( $post ) ) {
					wp_set_object_terms( $post->ID, $insert_term['term_id'], 'hotel_extra_service', true );
				}

				$this->write_log( sprintf( esc_html__( 'Migrated service "%s" mapping with new ID #%s', 'awebooking' ), $old_service['option_name'], $insert_term['term_id'] ), 'debug' );
			} else {
				$this->write_log( sprintf( esc_html__( 'Migrate service "%s" has failed.', 'awebooking' ), $old_service['option_name'] ), 'warning' );
			}
		}

		// Drop pricing table after done.
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}apb_booking_options" );
	}

	/**
	 * Doing migrate pricing table.
	 *
	 * @return void
	 */
	protected function doing_migrate_pricing() {
		global $wpdb;

		$this->write_log( esc_html__( '* Migrating pricing table', 'awebooking' ), 'info' );

		$pricings = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}apb_pricing", ARRAY_A );

		foreach ( $pricings as $row ) {
			foreach ( $row as $column => &$column_value ) {
				if ( in_array( $column, [ 'unit_id', 'year', 'month' ] ) ) {
					$column_value = (int) $column_value;
				} else {
					$column_value = Formatting::decimal_to_amount( (float) $column_value );
				}
			}

			// Find the room-type firt, if exists we'll insert into the database.
			$room_type = get_post( $row['unit_id'] );
			if ( $room_type ) {
				unset( $row['unit_id'] );
				$row['rate_id'] = $room_type->ID;

				$wpdb->insert( $wpdb->prefix . 'awebooking_pricing', $row, '%d' );
			}
		} // End foreach().

		// Drop pricing table after done.
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}apb_pricing" );

		$this->write_log( esc_html__( 'Migrated pricing table', 'awebooking' ), 'success' );
	}

	/**
	 * Doing migrate availability table.
	 *
	 * @return void
	 */
	protected function doing_migrate_availability() {
		global $wpdb;

		$this->write_log( esc_html__( '* Migrating availability table', 'awebooking' ), 'info' );

		$pricings = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}apb_availability", ARRAY_A );

		foreach ( $pricings as $row ) {
			foreach ( $row as $column => &$column_value ) {
				if ( in_array( $column, [ 'unit_id', 'year', 'month' ] ) ) {
					$column_value = (int) $column_value;
				} else {
					$column_value = isset( $this->mapping_states[ $column_value ] ) ? $this->mapping_states[ $column_value ] : 0;
				}
			}

			$uid = $row['unit_id'];
			if ( isset( $this->mapping_rooms[ $uid ] ) && $this->mapping_rooms[ $uid ] ) {
				unset( $row['unit_id'] );
				// Set with new room we just inserted.
				$row['room_id'] = $this->mapping_rooms[ $uid ];

				$wpdb->insert( $wpdb->prefix . 'awebooking_availability', $row, '%d' );
			}
		} // End foreach().

		// Drop pricing table after done.
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}apb_availability" );

		$this->write_log( esc_html__( 'Migrated availability table', 'awebooking' ), 'success' );
	}

	/**
	 * Nothing to migrations, we just delete old settings.
	 *
	 * @return void
	 */
	protected function doing_migrate_options() {
		global $wpdb;

		$options = $wpdb->get_results( "SELECT * FROM `{$wpdb->options}` WHERE `option_name` LIKE '%apb%'", ARRAY_A );
		foreach ( $options as $option ) {
			delete_option( $option['option_name'] );
		}
	}

	/**
	 * Doing migrate bookings.
	 *
	 * @return void
	 */
	protected function doing_migrate_bookings() {
		global $wpdb;

		$old_bookings = $wpdb->get_results( "SELECT wp_posts.* FROM wp_posts
			INNER JOIN wp_postmeta AS min_night ON ( wp_posts.ID = min_night.post_id AND min_night.meta_key = 'apb_data_order' )
			WHERE wp_posts.post_type = 'shop_order'
			ORDER BY wp_posts.ID DESC" );

		foreach ( $old_bookings as $old_booking ) {
			$working_id = $old_booking->ID;

			// Re-setup status.
			$new_status = isset( $this->mapping_status[ $old_booking->post_status ] ) ? $this->mapping_status[ $old_booking->post_status ] : Booking::PENDING;
			$wpdb->update( $wpdb->posts, array( 'post_status' => $new_status ), array( 'ID' => $working_id ) );

			// Set room_type post type.
			set_post_type( $working_id, 'awebooking' );

			// Now we'll touch metadata.
			$metadata = get_post_meta( $working_id );
			foreach ( $metadata as $meta_key => &$meta_value ) {
				$meta_value = ( 1 === count( $meta_value ) ) ? maybe_unserialize( $meta_value[0] ) : array_map( 'maybe_unserialize', $meta_value );
			}

			// Save booking.
			$booking = new Booking( $working_id );

			$booking['customer_note']       = $old_booking->post_excerpt;
			$booking['customer_first_name'] = $metadata['_billing_first_name'];
			$booking['customer_last_name']  = $metadata['_billing_last_name'];
			$booking['customer_email']      = $metadata['_billing_email'];
			$booking['customer_phone']      = $metadata['_billing_phone'];
			$booking['customer_company']    = $metadata['_billing_company'];

			$data_order = isset( $metadata['apb_data_order'][0] ) ? $metadata['apb_data_order'][0] : null;
			if ( is_array( $data_order ) ) {
				$booking['customer_id'] = $data_order['custommer'];

				$booking['adults']      = $data_order['room_adult'];
				$booking['children']    = $data_order['room_child'];
				$booking['check_in']    = $this->to_standard_date_format( $data_order['from'] );
				$booking['check_out']   = $this->to_standard_date_format( $data_order['to'] );

				$booking['total']       = $data_order['total_price'];
				$booking['currency']    = $meta_value['_order_currency'];

				if ( isset( $this->mapping_rooms[ $data_order['order_room_id'] ] ) ) {
					$booking['room_id'] = $this->mapping_rooms[ $data_order['order_room_id'] ];
				}
			}

			$booking->save();
		} // End foreach().
	}

	/**
	 * Format old standard "m/d/Y" of AweBooking to "Y-m-d" of new.
	 *
	 * @param  string $date Date string with "m/d/Y" format.
	 * @return string
	 */
	protected function to_standard_date_format( $date ) {
		try {
			return Carbon::createFromFormat( 'm/d/Y', $date )->toDateString();
		} catch ( \Exception $e ) {
			return '';
		}
	}
}
