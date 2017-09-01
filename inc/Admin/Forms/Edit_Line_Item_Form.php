<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Factory;
use AweBooking\AweBooking;
use AweBooking\Hotel\Service;
use AweBooking\Booking\Items\Line_Item;
use AweBooking\Booking\Items\Service_Item;

class Edit_Line_Item_Form extends Form_Abstract {
	/**
	 * Form ID.
	 *
	 * @var string
	 */
	protected $form_id = 'edit_booking_form';

	/**
	 * Booking item instance.
	 *
	 * @var string
	 */
	protected $line_item;

	/**
	 * Create edit form.
	 *
	 * @param Line_Item $line_item The booking item instance.
	 */
	public function __construct( Line_Item $line_item ) {
		parent::__construct();
		$this->line_item = $line_item;
	}

	/**
	 * Register fields in to the CMB2.
	 *
	 * @return void
	 */
	protected function fields() {
		$this->add_field([
			'id'          => 'edit_check_in_out',
			'type'        => 'date_range',
			'name'        => esc_html__( 'Check-in/out', 'awebooking' ),
			'validate'    => 'date_period',
			'attributes'  => [ 'placeholder' => AweBooking::DATE_FORMAT, 'tabindex' => '-1' ],
			'date_format' => AweBooking::DATE_FORMAT,
			'locked'      => true,
		]);

		$this->add_field([
			'id'               => 'edit_adults',
			'type'             => 'select',
			'name'             => esc_html__( 'Number of adults', 'awebooking' ),
			'default'          => 1,
			'validate'         => 'required|numeric|min:1',
			'validate_label'   => esc_html__( 'Adults', 'awebooking' ),
			'sanitization_cb'  => 'absint',
		]);

		$this->add_field([
			'id'              => 'edit_children',
			'type'            => 'select',
			'name'            => esc_html__( 'Number of children', 'awebooking' ),
			'default'         => 0,
			'validate'        => 'required|numeric|min:0',
			'sanitization_cb' => 'absint',
		]);

		$this->add_field([
			'id'              => 'edit_price',
			'type'            => 'text_small',
			'name'            => esc_html__( 'Total price', 'awebooking' ),
			'validate'        => 'required|price',
			'sanitization_cb' => 'awebooking_sanitize_price',
		]);

		$this->add_field([
			'id'              => 'edit_services',
			'type'            => 'multicheck',
			'name'            => esc_html__( 'Services', 'awebooking' ),
		]);
	}

	/**
	 * Handle process the form.
	 *
	 * @param  array|null $data        An array input data, if null $_POST will be use.
	 * @param  boolean    $check_nonce Run verity nonce from request.
	 * @return Line_Item|false
	 */
	public function handle( array $data = null, $check_nonce = true ) {
		$sanitized = parent::handle( $data, $check_nonce );

		if ( empty( $data['line_item_id'] ) ) {
			return false;
		}

		$line_item = $this->line_item;
		foreach ( [ 'edit_adults', 'edit_children', 'edit_price' ] as $key ) {
			if ( isset( $sanitized[ $key ] ) ) {
				$item_key = str_replace( 'edit_', '', $key );
				$line_item[ $item_key ] = $sanitized[ $key ];
			}
		}

		if ( isset( $sanitized['edit_check_in_out'] ) ) {
			$line_item['check_in'] = $sanitized['edit_check_in_out'][0];
			$line_item['check_out'] = $sanitized['edit_check_in_out'][1];
		}

		$line_item->save();

		// TODO: ...
		$edit_services = ( isset( $sanitized['edit_services'] ) && is_array( $sanitized['edit_services'] ) ) ? $sanitized['edit_services'] : array();
		$edit_services = array_map( 'absint', $edit_services );

		$the_booking = $this->line_item->get_booking();
		$line_item_services = $the_booking
			->get_service_items()
			->where( 'parent_id', $this->line_item->get_id() );

		$delete_ids = $line_item_services
			->pluck( 'service_id' )
			->diff( $edit_services )
			->toArray();

		$add_ids = array_diff( $edit_services, $delete_ids );

		foreach ( $delete_ids as $delete_id ) {
			$delete_service = $line_item_services
				->first( function( $item ) use ( $delete_id ) {
					return $item['service_id'] === (int) $delete_id;
				});

			$the_booking->remove_item( $delete_service );
		}

		foreach ( $add_ids as $add_item_id ) {
			$service = new Service( $add_item_id );
			if ( ! $service->exists() ) {
				continue;
			}

			$service_item = new Service_Item;
			$service_item['name']       = $service->get_name();
			$service_item['parent_id']  = $line_item->get_id();
			$service_item['service_id'] = $service->get_id();
			$service_item['price']      = $service->get_price()->get_amount();

			$the_booking->add_item( $service_item );
		}

		$the_booking->save();

		return true;
	}

	/**
	 * Setup the fields value, attributes, etc...
	 *
	 * @return void
	 */
	public function setup_fields() {
		$this['edit_price']->set_value(
			$this->line_item->get_subtotal()
		);

		$this['edit_check_in_out']->set_value([
			$this->line_item->get_check_in(),
			$this->line_item->get_check_out(),
		]);

		$room_type = $this->line_item->get_room_unit()->get_room_type();
		$a = range( 1, $room_type->get_allowed_adults() );
		$b = range( 0, $room_type->get_allowed_children() );

		$this['edit_adults']
			->set_value( $this->line_item->get_adults() )
			->set_prop( 'options', array_combine( $a, $a ) );

		$this['edit_children']
			->set_value( $this->line_item->get_children() )
			->set_prop( 'options', array_combine( $b, $b ) );

		$services = collect( $room_type->get_services() );
		$current_services = $this->line_item->get_booking()
			->get_service_items()
			->where( 'parent_id', $this->line_item->get_id() )
			->pluck( 'service_id' )
			->toArray();

		$this['edit_services']->set_prop( 'default', $current_services );
		$this['edit_services']->set_prop( 'options', $services->pluck( 'name', 'id' )->toArray() );

		printf( '<input type="hidden" name="line_item_id" value="%d" />', esc_attr( $this->line_item->get_id() ) );
	}
}
