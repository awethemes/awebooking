<?php
/**
 * Print the field content.
 *
 * @package AweBooking
 *
 * @var $field, $escaped_value, $object_id, $object_type, $types
 */

$path = $field->prop( 'include' );
if ( $path && file_exists( realpath( $path ) ) ) {
	include realpath( $path );
}
