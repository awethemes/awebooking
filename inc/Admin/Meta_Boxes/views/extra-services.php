<?php
use AweBooking\AweBooking;
use AweBooking\Service_Tax;
use AweBooking\Service;

$operation_options = Service_Tax::operation_options();
$type_options = Service_Tax::type_options();

$all_services = get_terms( AweBooking::HOTEL_SERVICE, array(
	'hide_empty' => false,
) );

foreach ( $all_services as &$term ) {
	$term = new Service( $term->term_id );
}

?>
<script type="text/javascript">
	var ABKNG_CURRENT_SERVICES = <?php echo json_encode( $room_type->get_services() ); ?>;
	var ABKNG_ALL_SERVICES = <?php echo json_encode( $all_services ); ?>;
</script>

<?php wp_nonce_field( 'awebooking-sync-services', '_awebooking_nonce' ); ?>

<div class="cmb-td" id="extra-services">
	<div class="cmb-row">
		<div class="cmb-th">
			<label for="extra-services"><?php esc_html_e( 'Choose an Extra Service', 'awebooking' ); ?></label>
		</div>
		<div class="cmb-td">
			<div class="skeleton-input-group">
				<select class="cmb2_select" v-model="serviceExist">
					<option :value="service.id" :disabled="checkIncludeService(service.id)" v-for="(service, index) in all_services">{{ service.name }}</option>
				</select>
				<input type="button" class="button" value="<?php esc_html_e( 'Add', 'awebooking' ); ?>" @click.prevent="addNewServiceExist()">
			</div>
		</div>
	</div>

	<div class="cmb-row cmb-type-group cmb-repeatable-group">
		<div class="postbox cmb-row cmb-repeatable-grouping awebooking-service-item" v-for="(service, index) in services">
			<input type="hidden" name="awebooking_services[]" :value="service.id">
			<h3 class="cmb-group-title cmbhandle-title">
				<span v-text="buildTitle(service)"></span>
			</h3>
			<button type="button" class="dashicons-before dashicons-no-alt cmb-remove-group-row" :title="service.name" @click.prevent="deleteService(index)"></button>
		</div>
	</div>

	<div class="cmb-row">
		<a href="#" class="hide-if-no-js taxonomy-add-new" v-on:click.stop.prevent="showAddNewContent = !showAddNewContent">
			<?php esc_html_e( '+ Add New Extra Service', 'awebooking' ); ?>
		</a>
	</div>
	<div class="add-new-service-content" v-if="showAddNewContent">
		<div class="cmb-row">
			<div class="cmb-th">
				<label for="name"><?php esc_html_e( 'Name', 'awebooking' ); ?></label>
			</div>
			<div class="cmb-td">
				<div class="skeleton-input-group">
					<input type="text" id="extra_service_name" value="" v-model="newService.name">
				</div>
			</div>
		</div>
		<div class="cmb-row">
			<div class="cmb-th">
				<label for="operation"><?php esc_html_e( 'Operation', 'awebooking' ); ?></label>
			</div>
			<div class="cmb-td">
				<div class="skeleton-input-group">
					<select class="cmb2_select" v-model="newService.operation" id="operation">
						<?php foreach ( $operation_options as $key => $operation_option ) : ?>
							<option value="<?php print $key; // WPCS: xss ok. ?>"><?php print $operation_option; // WPCS: xss ok. ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="skeleton-input-group">
					<input type="text" id="extra_service_price" value="" v-model="newService.price">
					<span class="skeleton-input-group__addon">
						<span class="awebooking-price-field" v-show="!isPricable()"><?php echo awebooking( 'currency' )->get_symbol(); ?></span>
						<span class="awebooking-value-field" v-show="isPricable()"><?php echo esc_html__( '%', 'awebooking' ); ?></span>
					</span>
				</div>
			</div>
		</div>
		<div class="cmb-row">
			<div class="cmb-th">
				<label for="type"><?php esc_html_e( 'Type', 'awebooking' ); ?></label>
			</div>
			<div class="cmb-td">
				<div class="skeleton-input-group">
					<select class="cmb2_select" v-model="newService.type">
						<?php foreach ( $type_options as $key => $type_option ) : ?>
							<option value="<?php print $key; // WPCS: xss ok. ?>"><?php print $type_option; // WPCS: xss ok. ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>
		<div class="cmb-row">
			<div class="cmb-td">
				<div class="skeleton-input-group">
					<input type="button" class="button" value="<?php esc_html_e( 'Add New Extra Service', 'awebooking' ); ?>" @click.prevent="addNewService()">
				</div>
			</div>
		</div>
	</div>
</div>
