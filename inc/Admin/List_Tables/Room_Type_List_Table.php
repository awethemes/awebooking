<?php
namespace AweBooking\Admin\List_Tables;

use AweBooking\AweBooking;

class Room_Type_List_Table extends Post_Type_Abstract {
	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	protected $post_type = AweBooking::ROOM_TYPE;

	/**
	 * Init somethings hooks.
	 *
	 * @access private
	 */
	public function init() {
		add_filter( 'request', array( $this, 'request_query' ) );
		add_filter( 'parse_query', array( $this, 'filters_query' ) );
		add_filter( 'restrict_manage_posts', array( $this, 'restrict_manage_rooms' ) );
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
		$columns = [
			'cb'              => '<input type="checkbox" />',
			'thumb'           => sprintf( '<span class="awebooking-column-image" title="%1$s">%1$s</span>', esc_html__( 'Image', 'awebooking' ) ),
			'title'           => esc_html__( 'Title', 'awebooking' ),
			'start_price'     => esc_html__( 'Starting Price', 'awebooking' ),
			'number_of_rooms' => esc_html__( 'No. of rooms', 'awebooking' ),
			'capacity'        => esc_html__( 'Capacity', 'awebooking' ),
		];

		if ( awebooking()->is_multi_location() ) {
			$columns['location'] = esc_html__( 'Location', 'awebooking' );
		}

		$columns['date'] = esc_html__( 'Date', 'awebooking' );

		return $columns;
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
		$sortable_columns['start_price']     = 'price';
		// $sortable_columns['capacity']        = 'capacity';
		$sortable_columns['number_of_rooms'] = 'number_of_rooms';

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
		global $room_type;

		$base_price = $room_type->get_base_price();

		switch ( $column ) {
			case 'thumb':
				if ( has_post_thumbnail( $post_id ) ) {
					$thumbnail = get_the_post_thumbnail( $post_id, [ 38, 38 ] );
				} else {
					$thumbnail = '<span class="awebooking-no-image"></span>';
				}

				printf( '<a href="%1$s">%2$s</a>', esc_url( get_edit_post_link( $post_id ) ), $thumbnail );
				break;

			case 'start_price':
				printf( '<span class="awebooking-label %2$s">%1$s</span>',
					$base_price, $base_price->is_zero() ? 'awebooking-label--danger' : 'awebooking-label--success'
				);
				break;

			case 'capacity':
				printf( '<span class="">%1$d %2$s</span>',
					$room_type->get_number_adults(),
					_n( 'adult', 'adults', $room_type->get_number_adults(), 'awebooking' )
				);

				if ( $room_type['number_children'] ) {
					printf( ' &amp; <span class="">%1$d %2$s</span>',
						$room_type->get_number_children(),
						_n( 'child', 'children', $room_type->get_number_children(), 'awebooking' )
					);
				}
				break;

			case 'location':
				if ( $room_type['location_id'] ) {
					$location = $room_type->get_location();
					printf( '<a href="%1$s">%2$s</a>', esc_url( get_edit_term_link( $location->term_id ) ), $location->name );
				}
				break;

			case 'number_of_rooms':
				echo esc_html( $room_type->get_total_rooms() );
				break;
		} // End switch().
	}

	/**
	 * Display filter rooms type.
	 *
	 * @return void
	 */
	public function restrict_manage_rooms() {
		global $typenow, $wp_query;

		// Works only on room type page.
		if ( AweBooking::ROOM_TYPE !== $typenow ) {
			return;
		}

		if ( awebooking()->is_multi_location() ) {
			wp_dropdown_categories([
				'show_option_all' => esc_html__( 'All Location', 'awebooking' ),
				'taxonomy'        => AweBooking::HOTEL_LOCATION,
				'name'            => 'hotel-location', // Don't using "hotel_location", I got some trouble with this.
				'orderby'         => 'name',
				'selected'        => isset( $_REQUEST['hotel-location'] ) ? absint( $_REQUEST['hotel-location'] ) : '',
				'hierarchical'    => false,
				'show_count'      => true,
				'hide_empty'      => false,
			]);
		}
	}


	/**
	 * Filter the products in admin based on options.
	 *
	 * @param mixed $query //.
	 */
	public function filters_query( $query ) {
		global $typenow, $wp_query;


		// Works only on room type page.
		if ( AweBooking::ROOM_TYPE !== $typenow ) {
			return;
		}

		// Hotel location.
		if ( isset( $_REQUEST['hotel-location'] ) && '0' !== $_REQUEST['hotel-location'] ) {
			$query->query_vars['tax_query'][] = array(
				'taxonomy' => AweBooking::HOTEL_LOCATION,
				'terms'    => absint( $_REQUEST['hotel-location'] ),
				'field'    => 'id',
				'operator' => 'IN',
			);
		}
	}

	/**
	 * Filters and sorting handler.
	 *
	 * @param  array $query_vars WP query_vars property.
	 * @return array
	 */
	public function request_query( $query_vars ) {
		global $typenow, $wp_query;

		// Prevent actions if not current post type.
		if ( $this->post_type !== $typenow ) {
			return $query_vars;
		}

		// Sorting handler.
		if ( isset( $query_vars['orderby'] ) ) {
			switch ( $query_vars['orderby'] ) {
				case 'price':
					$query_vars = array_merge( $query_vars, [
						'meta_key'  => 'base_price',
						'orderby'   => 'meta_value_num',
					]);
					break;

				// TODO: Our custom orderby.
				case 'number_of_rooms':
					$query_vars = array_merge( $query_vars, [
						'orderby'   => 'booking_rooms',
					]);
					break;
			}
		}

		return $query_vars;
	}
}
