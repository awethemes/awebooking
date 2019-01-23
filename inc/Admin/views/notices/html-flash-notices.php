<?php
/**
 * HTML display flash notices.
 *
 * @package AweBooking\Admin\View
 */

/* @vars $messages \AweBooking\Support\Collection */

// Filter the HTML message (not overlay).
$html_messages = $messages->where( 'overlay', false )->all();

foreach ( $html_messages as $message ) {
	// Otherwise, just print the message.
	printf( '<div class="notice notice-%1$s %2$s" role="notice">%3$s</div>',
		esc_attr( $message['level'] ),
		( true === $message['important'] ) ? 'notice-important' : 'is-dismissible',
		wp_kses_post( wpautop( $message['message'] ) )
	);
}

// With dialog messages, we need some JS to display them.
$dialog_messages = $messages->where( 'overlay', true );
if ( $dialog_messages->isEmpty() ) {
	return;
}

// Transform messages to sweetalert2 data.
$dialog_messages = $dialog_messages->values()
	->transform(function ( $m ) {
		return [
			'type'  => sanitize_key( $m['level'] ),
			'title' => trim( esc_html( $m['title'] ) ),
			'text'  => trim( esc_html( $m['message'] ) ),
			'timer' => $m['important'] ? null : 5000,
		];
	})->all();

// Ensure the sweetalert2 is enqueued.
if ( ! wp_style_is( 'sweetalert2', 'enqueued' ) ) {
	wp_enqueue_style( 'sweetalert2' );
}

if ( ! wp_script_is( 'sweetalert2', 'enqueued' ) ) {
	wp_enqueue_script( 'sweetalert2' );
}

?><script type="text/javascript">
(function() {
	'use strict';

	var _notices = <?php print $dialog_messages ? json_encode( $dialog_messages ) : '[]'; ?>;

	function alertWithSwal(items) {
		var toast = swal.mixin({ toast: true, buttonsStyling: false, showCancelButton: false, showConfirmButton: true, confirmButtonClass: 'button' });
		toast.queue(items);
	}

	document.addEventListener('DOMContentLoaded', function() {
		window.swal ? alertWithSwal(_notices) : _notices.forEach(function(m) { alert(m.text); })
	});
})();
</script>
