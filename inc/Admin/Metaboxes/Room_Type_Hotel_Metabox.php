<?php

namespace AweBooking\Admin\Metaboxes;

use AweBooking\Constants;
use WPLibs\Http\Request;
use AweBooking\Admin\Metabox;

class Room_Type_Hotel_Metabox extends Metabox {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id       = 'awebooking-room-type-hotel';
		$this->title    = esc_html__( 'Select Hotel', 'awebooking' );
		$this->screen   = Constants::ROOM_TYPE;
		$this->context  = 'side';
	}

	/**
	 * {@inheritdoc}
	 */
	public function should_show() {
		return abrs_multiple_hotels();
	}

	/**
	 * Output the metabox.
	 *
	 * @param \WP_Post $post The WP_Post object.
	 */
	public function output( $post ) {
		global $the_room_type;

		if ( is_null( $the_room_type ) ) {
			$the_room_type = abrs_get_room_type( $post->ID );
		}

		// List the hotels.
		$hotels = abrs_list_hotels( [], true );
		$current_id = $the_room_type->get( 'hotel_id' );

		?>
		<select name="hotel_id" id="hotel_id" class="selectize" style="width: 100%;">
			<?php foreach ( $hotels as $hotel ) : ?>
				<option value="<?php echo esc_attr( $hotel->get_id() ); ?>" <?php selected( $current_id, $hotel->get_id() ); ?>><?php echo esc_html( $hotel->get( 'name' ) ); ?></option>
			<?php endforeach; ?>
		</select><?php // @codingStandardsIgnoreLine
	}

	/**
	 * Handle save the the metabox.
	 *
	 * @param \WP_Post                $post    The WP_Post object instance.
	 * @param \WPLibs\Http\Request $request The HTTP Request.
	 */
	public function save( $post, Request $request ) {
		$room_type = abrs_get_room_type( $post->ID );

		$room_type['hotel_id'] = absint( $request->get( 'hotel_id' ) );

		$room_type->save();
	}
}
