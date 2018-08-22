<?php
/**
 * Print the field content.
 *
 * @package AweBooking
 *
 * @var $field, $escaped_value, $object_id, $object_type, $types
 */

$include_path = $field->prop( 'include' );

if ( $include_path && file_exists( realpath( $include_path ) ) ) {
	include realpath( $include_path );
} else {
	trigger_error( 'A specified valid path must be provided.', E_USER_WARNING );
}
