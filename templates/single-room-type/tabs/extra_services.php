<?php
/**
 * Optional Extra Services tab
 *
 * This template can be overridden by copying it to yourtheme/awebooking/single-room-type/tabs/amenity.php.
 *
 * @author        Awethemes
 * @package       AweBooking/Templates
 * @version       1.0.0
 */

use AweBooking\Support\Formatting;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $room_type;

$heading = esc_html( apply_filters( 'awebooking/room_type_extra_services_heading', __( 'Extra Services', 'awebooking' ) ) );
?>

<?php if ( $heading ) : ?>
	<h2 hidden><?php echo esc_html( $heading ); ?></h2>
<?php endif; ?>

<?php foreach ( $room_type->get_services() as $service ) : ?>
	<?php $mandatory = ( 'mandatory' === $service->get_type()  ) ? 'checked="checked" disabled="disabled"' : ''; ?>
	<div class="awebooking-service__item">
		<input type="checkbox" id="extra_id_<?php echo esc_attr( $service->get_id() ); ?>" <?php echo esc_attr( $mandatory ); ?> name="awebooking_services[]" value="<?php echo esc_attr( $service->get_id() ); ?>">

		<label for="extra_id_<?php echo esc_attr( $service->get_id() ); ?>"><?php echo esc_html( $service->get_name() ) ?></label>
		<span><?php echo Formatting::get_extra_service_label( $service ); ?> <?php echo ( 'mandatory' === $service->get_type() ) ? '(*' . esc_html( $service->get_type() ) . ')' : ''; ?></span>

		<div class="awebooking-service__content">
			<?php if ( $service->get_description() ) : ?>
				<p><?php echo esc_html( $service->get_description() ) ?></p>
			<?php endif; ?>
		</div>
	</div>
<?php endforeach; ?>
