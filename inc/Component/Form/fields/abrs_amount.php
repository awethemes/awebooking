<?php
/**
 * Print the field content.
 *
 * @package AweBooking
 *
 * @var $field, $escaped_value, $object_id, $object_type, $types
 */

$description = $types->_desc();
$field->set_prop( 'description', '' );

?>

<div class="abrs-input-addon">
	<?php echo $types->text(); // @codingStandardsIgnoreLine ?>
	<label for="<?php echo esc_attr( $types->_id() ); ?>"><?php echo esc_html( abrs_currency_symbol( $field->prop( 'currency' ) ) ); ?></label>
</div>

<?php if ( $description ) : ?>
	<?php echo $description; // WPCS: XSS OK. ?>
<?php endif ?>
