<?php
namespace AweBooking\Admin\List_Tables;

use AweBooking\Constants;

class Service_List_Table extends Abstract_List_Table {
	/**
	 * The post type name.
	 *
	 * @var string
	 */
	protected $list_table = Constants::HOTEL_SERVICE;

	/**
	 * The service instance in current loop.
	 *
	 * @var \AweBooking\Model\Service
	 */
	protected $hotel_service;

	/**
	 * {@inheritdoc}
	 */
	public function define_columns( $columns ) {
		if ( empty( $columns ) && ! is_array( $columns ) ) {
			$columns = [];
		}

		// Temp remove columns, we will rebuild late.
		unset( $columns['title'], $columns['comments'], $columns['date'] );

		$show_columns                        = [];
		$show_columns['title']               = esc_html__( 'Title', 'awebooking' );
		$show_columns['amount']              = esc_html__( 'Amount', 'awebooking' );
		$show_columns['quantity_selectable'] = esc_html__( 'Qty Selectable?', 'awebooking' );

		return array_merge( $columns, $show_columns );
	}

	/**
	 * {@inheritdoc}
	 */
	protected function prepare_row_data( $post_id ) {
		if ( is_null( $this->hotel_service ) || $this->hotel_service->get_id() !== (int) $post_id ) {
			$the_hotel_service   = abrs_get_service( $post_id );
			$this->hotel_service = $the_hotel_service;
		}
	}

	/**
	 * Display the hotel column.
	 *
	 * @return void
	 */
	protected function display_amount_column() {
		$operations = abrs_get_service_operations();

		echo '<span class="abrs-badge abrs-badge--primary">';
		abrs_price( $this->hotel_service->get_amount() );
		echo '</span>';

		echo '<div>', $operations[ $this->hotel_service->get_operation() ], '</div>';
	}

	/**
	 * Display the rate column.
	 *
	 * @return void
	 */
	protected function display_quantity_selectable_column() {
		if ( $this->hotel_service->is_quantity_selectable() ) {
			echo '<i class="aficon aficon-checkmark-circle" style="color: #03A9F4; font-size: 20px;"></i>';
		}
	}
}
