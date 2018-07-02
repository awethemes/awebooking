<div id="tax_rates">
	<table class="widefat awebooking-input-table tax-rates-table" cellspacing="0">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Name', 'awebooking' ); ?></th>
				<th><?php esc_html_e( 'Single rate (%)', 'awebooking' ); ?></th>
				<th><?php esc_html_e( 'Priority', 'awebooking' ); ?></th>
				<th width="10%"><?php esc_html_e( 'Compound', 'awebooking' ); ?></th>
			</tr>
		</thead>

		<tbody class="accounts ui-sortable">
			<?php foreach ( abrs_get_tax_rates() as $key => $tax_rate ) : ?>
				<tr class="account ui-sortable-handle">
					<td>
						<input type="hidden" value="<?php echo esc_attr( $tax_rate['id'] ); ?>" name="tax_rates[<?php echo esc_attr( $key ); ?>][id]" />
						<input type="text" value="<?php echo esc_attr( $tax_rate['name'] ); ?>" name="tax_rates[<?php echo esc_attr( $key ); ?>][name]" />
					</td>
					<td>
						<input type="text" value="<?php echo floatval( $tax_rate['rate'] ); ?>" name="tax_rates[<?php echo esc_attr( $key ); ?>][rate]" />
					</td>
					<td>
						<input type="text" value="<?php echo esc_attr( $tax_rate['priority'] ); ?>" name="tax_rates[<?php echo esc_attr( $key ); ?>][priority]" />
					</td>
					<td style="text-align: center;">
						<input type="checkbox" value="<?php echo esc_attr( $tax_rate['compound'] ); ?>" name="tax_rates[<?php echo esc_attr( $key ); ?>][compound]" <?php checked( $tax_rate['compound'], 1 ); ?> />
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>

		<tfoot>
			<tr>
				<th colspan="4">
					<a href="#" class="add button"><?php esc_html_e( '+ Add tax rate', 'awebooking' ); ?></a>
					<a href="#" class="remove_rows button"><?php esc_html_e( 'Remove selected tax rate(s)', 'awebooking' ); ?></a>
				</th>
			</tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">
	jQuery(function ($) {
		$('#tax_rates').on('click', 'a.add', function () {
			var size = $('#tax_rates').find('tbody .account').length;

			$('<tr class="account">\
				<td>\
					<input type="hidden" name="tax_rates[' + size + '][id]" />\
					<input type="text" name="tax_rates[' + size + '][name]" />\
				</td>\
				<td><input type="text" name="tax_rates[' + size + '][rate]" /></td>\
				<td><input type="text" name="tax_rates[' + size + '][priority]" /></td>\
				<td style="text-align: center;"><input type="checkbox" name="tax_rates[' + size + '][compound]" /></td>\
			</tr>').appendTo('#tax_rates table tbody');

			return false;
		});
	});
</script>
