<?php
/**
 * Icon manager icon-box partial template.
 *
 * @author  Awethemes
 * @package Awecontent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="<?php echo esc_attr( sprintf( 'ac-icon-%s', $icon->id ) ); ?>" class="ac-icon-manager__box">
	<div class="postbox">

		<?php if ( $icon instanceof Iconpack_Uploaded ) : ?>
			<button type="button" data-toggle="dropdown" class="handlediv button-link" title="<?php echo esc_html__( 'A	ctions', 'awecontent' ); ?>">
				<span class="screen-reader-text"><?php echo esc_html__( 'Actions', 'awecontent' ); ?></span>
				<span class="toggle-indicator" aria-hidden="true"></span>
			</button>
		<?php endif; ?>

		<span class="postbox-search-icons">
			<label class="screen-reader-text" for="wp-filter-search-input"><?php echo esc_html__( 'Search icons...', 'awecontent' ); ?></label>
			<input placeholder="<?php echo esc_attr( esc_html__( 'Search icons...', 'awecontent' ) ); ?>" type="search" id="wp-filter-search-input" class="wp-filter-search fuzzy-search">
		</span>

		<h2 class="hndle">
			<?php if ( $icon ) : ?>
				<span class="dashicons dashicons-lock"></span>
			<?php endif; ?>

			<span class="icon_pack_id screen-reader-text"><?php echo esc_html( $icon->id ); ?></span>
			<span class="icon_pack_name"><?php echo esc_html( $icon->name ? $icon->name : $icon->id ); ?></span>
			<small class="count">(<?php echo esc_html( count( $icon->icons() ) ); ?>)</small>
		</h2>

		<div class="inside">
			<div class="inside-icons">
				<!-- <span class="spinner" style="visibility: visible;"></span> -->
				<ul class="ac-icon-manager__icons">
					<?php foreach ( $icon->icons() as $i ) : ?>
						<li><i class="<?php echo esc_attr( $icon->id ); ?> <?php echo esc_attr( $i['id'] ); ?>" aria-hidden="true"></i> <div class="zoom-icon"><i class="<?php echo esc_attr( $icon->id ); ?> <?php echo esc_attr( $i['id'] ); ?>" aria-hidden="true"></i><span class="icon-label name"><?php echo esc_html( $i['name'] ); ?></span> <span class="screen-reader-text classes"><?php echo esc_attr( $i['id'] ); ?></span></div></li>
					<?php endforeach ?>
				</ul>
			</div>
		</div>

		<?php if ( $icon ) : ?>
			<ul class="split-button-body">
				<!-- <li><button type="button" class="button-link update-button split-button-option"><?php echo esc_html__( 'Update Icon', 'awecontent' ); ?></button></li> -->
				<li><button type="button" data-icon="<?php echo esc_attr( $icon->name ); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>" class="button-link delete-button split-button-option"><?php echo esc_html__( 'Delete', 'awecontent' ); ?></button></li>
			</ul>
		<?php endif; ?>
	</div><!-- /.postbox -->
</div><!-- /.ac-icon-manager__box -->
