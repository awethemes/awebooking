<?php
/**
 * This's just a sample email (for preview in admin too).
 *
 * Demo how awebooking email template work.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abrs_mailer()->header( $email ); ?>

<h1>Heading 1: Paragraph</h1>
<p>This is a paragraph filled with Lorem Ipsum and a link. Cumque dicta <a href="#">doloremque eaque</a>, enim error laboriosam pariatur possimus tenetur veritatis voluptas.</p>

<h2>Heading 2: Table</h2>
<table class="table" width="100%" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th class="align-left">Room</th>
			<th class="align-left">Description</th>
			<th class="align-right">Price</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Room 1</td>
			<td>Lorem Ipsum</td>
			<td class="align-right">$10</td>
		</tr>
		<tr>
			<td>Room 2</td>
			<td>Lorem ipsum dolor sit amet.</td>
			<td class="align-right">$20</td>
		</tr>
	</tbody>
</table>

<h3>Heading 3: Components</h3>

<p>Blockquote</p>
<blockquote>Smile, breathe and go slowly</blockquote>

<p>Panel</p>
<?php $email->component( 'panel', 'This is the panel content.' ); ?>

<p>Promotion</p>
<?php $email->component( 'promotion', 'Discount Code: 123456789' ); ?>

<p>Button</p>
<?php $email->component( 'button', 'Pay Now' ); ?>
<?php $email->component( 'button', 'Cancel Booking', [ 'color' => 'red' ] ); ?>
<?php $email->component( 'button', 'View Your Booking', [ 'color' => 'green' ] ); ?>

<p>Subcopy</p>
<?php $email->component( 'subcopy', 'If you\'re having trouble clicking the button, copy and paste the URL below into your web browser: ...' ); ?>

<?php abrs_mailer()->footer( $email ); //@codingStandardsIgnoreLine
