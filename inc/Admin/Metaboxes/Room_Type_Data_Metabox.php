<?php

namespace AweBooking\Admin\Metaboxes;

use AweBooking\Constants;
use AweBooking\Model\Room;
use AweBooking\Model\Room_Type;
use AweBooking\Admin\Metabox;
use AweBooking\Admin\Forms\Room_Type_Data_Form;
use WPLibs\Http\Request;
use Illuminate\Support\Arr;

class Room_Type_Data_Metabox extends Metabox {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id     = 'awebooking-room-type-data';
		$this->title  = esc_html__( 'Room Type Data', 'awebooking' );
		$this->screen = Constants::ROOM_TYPE;
	}

	/**
	 * Output the metabox.
	 *
	 * @param \WP_Post $post The WP_Post object.
	 */
	public function output( $post ) {
		global $the_room_type;

		// Setup the room type object.
		if ( is_null( $the_room_type ) ) {
			$the_room_type = abrs_get_room_type( $post->ID );
		}

		// Is current room type is translation or not?
		$is_translation = null;
		if ( abrs_running_on_multilanguage() ) {
			$is_translation = (int) abrs_multilingual()->get_original_post( $post->ID ) !== (int) $post->ID;
		}

		// Setup the form.
		$form = new Room_Type_Data_Form(
			$this->on_edit_screen() ? $the_room_type : null
		);

		$form->prepare_fields();

		foreach ( $form as $control ) {
			/* @var $control \AweBooking\Component\Form\Field_Proxy */
			if ( $is_translation && ! $control->prop( 'translatable' ) ) {
				$control->set_attribute( 'disabled', 'disabled' );
			}
		}

		// Print the core nonce field.
		wp_nonce_field( 'awebooking_save_data', '_awebooking_nonce' );

		// Show the form controls.
		include trailingslashit( __DIR__ ) . 'views/html-room-type-main.php';
	}

	/**
	 * Handle save the the metabox.
	 *
	 * @param \WP_Post                $post    The WP_Post object instance.
	 * @param \WPLibs\Http\Request $request The HTTP Request.
	 */
	public function save( $post, Request $request ) {
		// Create the new room-type instance.
		$room_type = new Room_Type( $post->ID );

		$is_translation = false;
		if ( abrs_running_on_multilanguage() ) {
			$is_translation = (int) abrs_multilingual()->get_original_post( $post->ID ) !== (int) $post->ID;
		}

		$controls = new Room_Type_Data_Form( $room_type );

		// Get the sanitized values.
		$values = $controls->handle( $request )->all();

		// Correct the occupancy size.
		foreach ( [ 'number_adults', 'number_children', 'number_infants' ] as $key ) {
			if ( isset( $values[ $key ] ) ) {
				$max = (int) isset( $values['maximum_occupancy'] ) ? absint( $values['maximum_occupancy'] ) : 0;

				// Value cannot be greater than maximum occupancy.
				if ( (int) $values[ $key ] > $max ) {
					$values[ $key ] = $max;
				}
			}
		}

		// Prevent save the non-translatable fields.
		if ( $is_translation ) {
			$nontranslatable = abrs_collect( $controls->prop( 'fields' ) )
				->where( 'translatable', false )
				->pluck( 'id' )
				->all();

			Arr::forget( $values, $nontranslatable );
		}

		// Fill data values.
		$room_type->fill( $values );

		if ( empty( $values['rate_services'] ) ) {
			$room_type['rate_services'] = [];
		}

		// Fire action before save.
		do_action( 'abrs_process_room_type_data', $room_type, $values, $request );

		// Save the data.
		$saved = $room_type->save();

		// Handle update rooms data.
		if ( ! $is_translation ) {
			if ( 0 === count( $room_type->get_rooms() ) ) {
				$this->perform_scaffold_rooms( $room_type, $request->input( '_rooms', [] ) );
			} elseif ( $request->filled( '_rooms' ) ) {
				$this->perform_update_rooms( $room_type, $request->input( '_rooms', [] ) );
			}
		}

		// Add successfully notice.
		if ( $saved ) {
			abrs_flash_notices( 'Successfully updated', 'success' )->dialog();
		}
	}

	/**
	 * Perform scaffold rooms.
	 *
	 * @param  \AweBooking\Model\Room_Type $room_type      The room type instance.
	 * @param  array                       $scaffold_rooms The rooms data.
	 * @return void
	 */
	protected function perform_scaffold_rooms( $room_type, $scaffold_rooms ) {
		$scaffold_rooms = array_filter( (array) $scaffold_rooms );

		if ( empty( $scaffold_rooms ) ) {
			return;
		}

		foreach ( array_values( $scaffold_rooms ) as $index => $data ) {
			if ( empty( $data['id'] ) || -1 !== (int) $data['id'] ) {
				continue;
			}

			$room = ( new Room )->fill([
				'order'     => $index,
				'name'      => ! empty( $data['name'] )
					? sanitize_text_field( wp_unslash( $data['name'] ) )
					/* translators: 1: Room type name, 2: Room item order */
					: sprintf( esc_html__( '%1$s - %2$d', 'awebooking' ), $room_type->get( 'title' ), $index + 1 ),
				'room_type' => $room_type->get_id(),
			]);

			$room->save();
		}
	}

	/**
	 * Perform update rooms.
	 *
	 * @param  \AweBooking\Model\Room_Type $room_type The room type instance.
	 * @param  array                       $rooms     The rooms data.
	 * @return void
	 */
	protected function perform_update_rooms( $room_type, $rooms ) {
		$index = 0;

		foreach ( (array) $rooms as $data ) {
			$name = ! empty( $data['name'] )
				? sanitize_text_field( wp_unslash( $data['name'] ) )
				/* translators: 1: Room type name, 2: Room item order */
				: sprintf( esc_html__( '%1$s - %2$d', 'awebooking' ), $room_type->get( 'title' ), $index + 1 );

			if ( $room = abrs_get_room( $data['id'] ) ) {
				$room->order = $index;
				$room->name  = $name;
			} else {
				$room = ( new Room )->fill([
					'name'      => $name,
					'order'     => $index,
					'room_type' => $room_type->get_id(),
				]);
			}

			$room->save();
			$index++;
		}
	}
}
