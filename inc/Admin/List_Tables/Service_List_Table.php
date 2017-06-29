<?php
namespace AweBooking\Admin\List_Tables;

use AweBooking\Service;
use AweBooking\AweBooking;
use AweBooking\Support\Formatting;

class Service_List_Table extends Taxonomy_Abstract {
	/**
	 * Taxonomy slug.
	 *
	 * @var string
	 */
	protected $taxonomy = AweBooking::HOTEL_SERVICE;

	/**
	 * Registers columns to display in the terms list table.
	 *
	 * @access private
	 *
	 * @param  array $columns Array of registered column names/labels.
	 * @return array
	 */
	public function columns( $columns ) {
		return [
			'cb'       => '<input type="checkbox" />',
			'name'     => esc_html__( 'Name', 'awebooking' ),
			'details'  => esc_html__( 'Price', 'awebooking' ),
			'type'     => esc_html__( 'Type', 'awebooking' ),
			'posts'    => esc_html__( 'Count', 'awebooking' ),
		];
	}

	/**
	 * Handles display columns in the terms list table.
	 *
	 * @access private
	 *
	 * @param string $content     The column content.
	 * @param string $column_name Name of the column.
	 * @param int    $term_id     Term ID.
	 */
	public function columns_display( $content, $column_name, $term_id ) {
		$service = new Service( $term_id );

		switch ( $column_name ) {
			case 'type':
				printf( '<span class="awebooking-label %2$s">%1$s</span>',
					( $service->is_optional() ? esc_html__( 'Optional', 'awebooking' ) : esc_html__( 'Mandatory', 'awebooking' ) ),
					( $service->is_optional() ? 'awebooking-label--info' : 'awebooking-label--warning' )
				);
				break;

			case 'details':
				echo Formatting::get_extra_service_label( $service );
				break;
		} // End switch().
	}
}
