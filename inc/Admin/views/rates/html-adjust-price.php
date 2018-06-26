<?php
/**
 * Partial html to display adjust price form.
 *
 * @package AweBooking
 */

// Create the form builder.
$controls = abrs_create_form( 'adjust-price' );

?><div id="scheduler-form-dialog" class="awebooking-dialog-contents" title="<?php echo esc_html__( 'Adjust Price', 'awebooking' ); ?>" style="display: none;">
	<form method="POST" action="<?php echo esc_url( abrs_admin_route( '/rates' ) ); ?>">
		<?php wp_nonce_field( 'awebooking_update_price' ); ?>

		<h2 class="screen-reader-text"><?php echo esc_html__( 'Set room price', 'awebooking' ); ?></h2>

		<div id="js-scheduler-form-controls"></div>

		<div class="awebooking-dialog-buttons">
			<button type="submit" class="button button-primary abrs-button"><?php echo esc_html__( 'Submit', 'awebooking' ); ?></button>
		</div>
	</form>
</div>

<script type="text/template" id="tmpl-scheduler-pricing-controls">
	<input type="hidden" name="rate" value="{{ data.calendar }}">

	<# if (data.roomtype) { #>
		<h3 class="abrs-mt0" style="margin-bottom: 10px;">{{ data.roomtype.title }}</h3>
	<# } #>

	<div class="cmb2-wrap awebooking-wrap">
		<div class="cmb2-inline-metabox">
			<?php
			$controls->show_field([
				'id'          => 'date',
				'type'        => 'abrs_dates',
				'name'        => esc_html__( 'Select dates', 'awebooking' ),
				'input_names' => [ 'start_date', 'end_date' ],
				'default'     => [ '{{ data.startDate }}', '{{ data.endDate }}' ],
				'attributes'  => [ 'tabindex' => '-1' ],
				'show_js'     => false,
			]);
			?>

			<div class="cmb-row">
				<div class="cmb-th"><label for="amount"><?php esc_html_e( 'Amount', 'awebooking' ); ?></label></div>

				<div class="cmb-td">
					<div class="abrs-input-addon group-operator-amount">
						<select class="cmb2_select" name="operator" id="operator" tabindex="-1">
							<?php foreach ( abrs_get_rate_operations() as $key => $label ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( 'replace', $key ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach ?>
						</select>

						<input type="text" class="cmb2-text-small" name="amount" id="amount" value="{{ data.amount }}" tabindex="1">
					</div>

					<p class="cmb2-metabox-description"><?php esc_html_e( 'Note: Replace with zero will be reset custom price.', 'awebooking' ); ?></p>
				</div>
			</div>

			<?php
			$controls->show_field([
				'id'                => 'days',
				'type'              => 'multicheck_inline',
				'name'              => esc_html__( 'Apply on days', 'awebooking' ),
				'default'           => [ 0, 1, 2, 3, 4, 5, 6 ],
				'attributes'        => [ 'tabindex' => '-1' ],
				'select_all_button' => false,
				'options'           => abrs_days_of_week( 'abbrev' ),
			]);
			?>
		</div><!-- /.cmb2-inline-metabox -->
	</div><!-- /.awebooking-wrap -->
</script>
