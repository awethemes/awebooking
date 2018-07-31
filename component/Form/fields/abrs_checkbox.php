<?php
/**
 * Print the field content.
 *
 * @package AweBooking
 *
 * @var $field, $escaped_value, $object_id, $object_type, $types
 */

// @codingStandardsIgnoreLine
echo $types->checkbox( [], 'on' === abrs_sanitize_checkbox( $escaped_value ) );
