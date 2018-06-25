<div id="bacs_accounts">
	<table class="widefat awebooking-input-table" cellspacing="0">
		<thead>
			<tr>
				<th>name</th>
				<th>rate &</th>
			</tr>
		</thead>

		<tbody class="accounts ui-sortable">
			<?php foreach ( abrs_get_tax_rates() as $tax_rate ) : ?>
				<tr class="account ui-sortable-handle">
					<td><input type="text" value="<?php echo esc_attr( $tax_rate['name'] ); ?>" name="tax_rates[<?php echo esc_attr( $tax_rate['id'] ); ?>]"></td>
					<td><input type="text" value="" name="bacs_bic[0]"></td>
				</tr>
			<?php endforeach; ?>
		</tbody>

		<tfoot>
		<tr>
			<th colspan="7"><a href="#" class="add button">+ Add account</a> <a href="#" class="remove_rows button">Remove
					selected account(s)</a></th>
		</tr>
		</tfoot>
	</table>
</div>
<script type="text/javascript">
	jQuery(function ($) {
		$('#bacs_accounts').on('click', 'a.add', function () {

			var size = $('#bacs_accounts').find('tbody .account').length;

			$('<tr class="account">\
									<td class="sort"></td>\
									<td><input type="text" name="bacs_account_name[' + size + ']" /></td>\
									<td><input type="text" name="bacs_account_number[' + size + ']" /></td>\
									<td><input type="text" name="bacs_bank_name[' + size + ']" /></td>\
									<td><input type="text" name="bacs_sort_code[' + size + ']" /></td>\
									<td><input type="text" name="bacs_iban[' + size + ']" /></td>\
									<td><input type="text" name="bacs_bic[' + size + ']" /></td>\
								</tr>').appendTo('#bacs_accounts table tbody');

			return false;
		});
	});
</script>
