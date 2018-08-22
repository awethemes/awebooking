<?php
/**
 * Print the field content.
 *
 * @package Coming2Live
 *
 * @var $field, $escaped_value, $object_id, $object_type, $types
 */

$description = $types->_desc();
$field->set_prop( 'description', '' );

?>

<div class="cmb2-onoffswitch">
	<?php echo $types->checkbox( [], 'on' === abrs_sanitize_checkbox( $escaped_value ) ); // @codingStandardsIgnoreLine ?>
</div>

<label for="<?php echo esc_attr( $types->_id() ); ?>"><?php print $description; // WPCS: XSS OK. ?></label>
