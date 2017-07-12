<?php
$text_align = is_rtl() ? 'right' : 'left';

echo __( "Your order is on-hold until we confirm payment has been received. Your order details are shown below for your reference:", 'awebooking' ) . "\n\n";

?>
<h2 style="margin-top: 50px;"><?php printf( __( 'Order #%s', 'awebooking' ), 10 ); ?></h2>

<div class="table">
	<table>
		<thead>
			<tr>
				<th colspan="2" style="text-align: left;">Room type: Luxury Room</th>
				<th style="text-align: right;">Price</th>
			</tr>

		</thead>
		<tbody>
			<tr>
				<td colspan="3" style="text-align: left;"><b>Detail</b></td>
			</tr>

			<tr>
				<td colspan="2" style="text-align: left;">From July 11, 2017 to July 12, 2017, 1 nights</td>
				<td style="text-align: right;">$120</td>
			</tr>

			<tr>
				<td colspan="3" style="text-align: left;"><b>Extra services</b></td>
			</tr>

			<tr>
				<td colspan="2" style="text-align: left;">Televison</td>
				<td style="text-align: right;">$20</td>
			</tr>

		</tbody>
	</table>
</div>

<div class="table">
	<table>
		<thead>
			<tr>
				<th colspan="2" style="text-align: left;"><b>Total</b></th>
				<th style="text-align: right;"><b>$140</b></th>
			</tr>

		</thead>
	</table>
</div>
