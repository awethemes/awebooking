<?php
namespace AweBooking\Admin\List_Tables;

use AweBooking\Constants;
use AweBooking\Model\Common\Guest_Counts;

class Room_Type_List_Table extends Abstract_List_Table {
	/**
	 * The post type name.
	 *
	 * @var string
	 */
	protected $list_table = Constants::ROOM_TYPE;

	/**
	 * The current loop room type.
	 *
	 * @var \AweBooking\Model\Room_Type
	 */
	protected $room_type;

	/**
	 * {@inheritdoc}
	 */
	public function define_columns( $columns ) {
		if ( empty( $columns ) && ! is_array( $columns ) ) {
			$columns = [];
		}

		// Temp remove columns, we will rebuild late.
		unset( $columns['title'], $columns['comments'], $columns['date'] );

		$show_columns              = [];
		$show_columns['thumb']     = '<span class="tippy" title="' . esc_attr__( 'Image', 'awebooking' ) . '"><span class="dashicons dashicons-format-image"></span><span class="screen-reader-text">' . esc_html__( 'Image', 'awebooking' ) . '</span></span>';
		$show_columns['title']     = esc_html__( 'Title', 'awebooking' );
		$show_columns['rooms']     = esc_html__( 'Rooms', 'awebooking' );
		$show_columns['rate']      = esc_html__( 'Rack Rate', 'awebooking' );
		$show_columns['occupancy'] = esc_html__( 'Occupancy', 'awebooking' );
		$show_columns['date']      = esc_html__( 'Date', 'awebooking' );

		return array_merge( $columns, $show_columns );
	}

	/**
	 * {@inheritdoc}
	 */
	public function define_sortable_columns( $columns ) {
		return array_merge( $columns, [
			// 'rooms'    => 'rooms',
			'rate'      => 'rack_rate',
			'occupancy' => 'occupancy',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function prepare_row_data( $post_id ) {
		global $the_room_type;

		if ( is_null( $this->room_type ) || $this->room_type->get_id() !== (int) $post_id ) {
			$the_room_type   = abrs_get_room_type( $post_id );
			$this->room_type = $the_room_type;
		}
	}

	/**
	 * Display the thumbnail column.
	 *
	 * @param  int $post_id The post ID.
	 * @return void
	 */
	protected function display_thumb_column( $post_id ) {
		$thumbnail = '<span class="abrs-no-image"></span>';

		if ( has_post_thumbnail( $post_id ) ) {
			$thumbnail = get_the_post_thumbnail( $post_id, 'thumbnail' );
		}

		printf( '<a href="%1$s">%2$s</a>', esc_url( get_edit_post_link( $post_id ) ), $thumbnail ); // @wpcs: XSS OK.
	}

	/**
	 * Display the rate column.
	 *
	 * @return void
	 */
	protected function display_rooms_column() {
		echo '<span class="abrs-label abrs-label--info">' . esc_html( $this->room_type->get_total_rooms() ) . '</span>'; // WPCS: XSS OK.
	}

	/**
	 * Display the rate column.
	 *
	 * @return void
	 */
	protected function display_rate_column() {
		if ( 0 == $this->room_type['rack_rate'] ) {
			echo '<span class="abrs-badge abrs-badge--negative">' . abrs_format_price( 0 ) . '</span>'; // WPCS: XSS OK.
		} else {
			echo '<span class="abrs-badge abrs-badge--primary">' . abrs_format_price( $this->room_type['rack_rate'] ) . '</span>'; // WPCS: XSS OK.
		}
	}

	/**
	 * Display the occupancy column.
	 *
	 * @return void
	 */
	protected function display_occupancy_column() {
		$guests = new Guest_Counts( $this->room_type['number_adults'] );

		if ( abrs_children_bookable() && $this->room_type['number_children'] ) {
			$guests->set_children( $this->room_type['number_children'] );
		}

		if ( abrs_infants_bookable() && $this->room_type['number_infants'] ) {
			$guests->set_infants( $this->room_type['number_infants'] );
		}

		/* translators: %s Max occupancy */
		echo ' <span class="abrs-badge abrs-badge--primary">' . sprintf( esc_html__( 'Max %d', 'awebooking' ), absint( $this->room_type['maximum_occupancy'] ) ) . '</span>';
		print '<br>' . $guests->as_string(); // @wpcs: XSS OK.
	}

	/**
	 * Handle any custom filters.
	 *
	 * @param  array $query_vars Query vars.
	 * @return array
	 */
	protected function query_filters( $query_vars ) {
		// Handle custom order by.
		if ( ! empty( $query_vars['orderby'] ) ) {
			switch ( $query_vars['orderby'] ) {
				case 'rooms':
					$query_vars = array_merge( $query_vars, [
						'meta_key'  => '_cache_total_rooms',
						'orderby'   => 'meta_value_num',
					]);
					break;

				case 'rack_rate':
					$query_vars = array_merge( $query_vars, [
						'meta_key'  => 'base_price',
						'orderby'   => 'meta_value_num',
					]);
					break;

				case 'occupancy':
					$query_vars = array_merge( $query_vars, [
						'meta_key'  => '_maximum_occupancy',
						'orderby'   => 'meta_value_num',
					]);
					break;
			}
		}

		return $query_vars;
	}
}
