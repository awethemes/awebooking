<?php
/* @vars $cal, $_scheduler */

?><div class="scheduler__row">
	<div class="scheduler__days">
		<?php foreach ( $period as $date ) : ?>
			<div class="scheduler__column <?php echo esc_attr( implode( ' ', $cal->get_date_classes( $date ) ) ); ?>" data-date="<?php echo esc_attr( $date->format( 'Y-m-d' ) ); ?>">
				<span>0</span><br><span>33</span>
			</div>
		<?php endforeach; ?>
	</div>
</div>
