<?php
/**
 * This template show the search result item.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/search/result-item.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$remain_rooms = $room_rate->get_remain_rooms();

$rate_plan = $room_rate->get_rate_plan();
?>

<div class="roommaster">
	<div class="roommaster-header">
		<h3 class="roommaster-header__title">
			<a href="<?php echo esc_url( get_permalink( $room_type->get_id() ) ); ?>" rel="bookmark" target="_blank"><?php echo esc_html( $room_type->get( 'title' ) ); ?></a>
		</h3>
	</div>
	<div class="roommaster-wrapper awebooking">
		<div class="roommaster-content">
			<div class="columns no-gutters">
				<div class="column-3">
					<div class="roommaster-info">
						<div class="roommaster-info__title"><?php esc_html_e( 'Room type', 'awebooking' ); ?></div>
						<div class="roommaster-info__image">
							<?php
							if ( has_post_thumbnail( $room_type->get_id() ) ) {
								echo get_the_post_thumbnail( $room_type->get_id(), 'awebooking_archive' );
							}
							?>
						</div>

						<ul class="roommaster-info__list">
							<?php if ( $room_type->get( 'view' ) ) : ?>
								<li class="info-item">
									<span class="info-icon">
										<i class="aficon aficon-business"></i>
										<span class="screen-reader-text"><?php echo esc_html_x( 'Room view', 'room view button', 'awebooking' ); ?></span>
									</span>
									<?php echo esc_html( $room_type->get( 'view' ) ); ?>
								</li>
							<?php endif; ?>

							<?php if ( $room_type->get( 'area_size' ) ) : ?>
								<li class="info-item">
									<span class="info-icon">
										<i class="aficon aficon-elevator"></i>
										<span class="screen-reader-text"><?php echo esc_html_x( 'Area size', 'area size button', 'awebooking' ); ?></span>
									</span>
									<?php
										/* translators: %1$s area size, %2$s measure unit */
										printf( esc_html_x( '%1$s %2$s', 'room area size', 'awebooking' ),
											esc_html( $room_type->get( 'area_size' ) ),
											abrs_format_measure_unit_label()
										); // WPCS: xss ok.
									?>
								</li>
							<?php endif; ?>

							<?php if ( $room_type->get( 'beds' ) ) : ?>
								<li class="info-item">
									<span class="info-icon">
										<i class="aficon aficon-bed"></i>
										<span class="screen-reader-text"><?php echo esc_html_x( 'Bed', 'bed button', 'awebooking' ); ?></span>
									</span>
									<?php print abrs_get_room_beds( $room_type ); // WPCS: xss ok. ?>
								</li>
							<?php endif; ?>

							<?php do_action( 'awebooking/result_item/after_room_informations' ); ?>
						</ul>
					</div>
				</div>

				<div class="column-9">
					<div class="roommaster-list">
						<table class="roommaster-table">
							<thead>
								<tr>
									<th class="thead-1"><?php esc_html_e( 'Choose your deal', 'awebooking' ); ?></th>
									<th class="thead-2"><?php esc_html_e( 'Capacity', 'awebooking' ); ?></th>
									<th class="thead-3"><?php esc_html_e( 'Price', 'awebooking' ); ?></th>
									<th class="thead-5"></th>
								</tr>
							</thead>
							<tbody>


								<?php //foreach ($variable as $key => $value) : ?>
								<tr>
									<td class="roommaster-child">
										<?php if ( $room_type->get( 'rate_inclusions' ) ): ?>
											<div class="roommaster-child__item">
												<span class="roommaster-child__bucketspan"><?php esc_html_e( 'Inclusions', 'awebooking' ); ?></span>
												<?php foreach ( $room_type->get( 'rate_inclusions' ) as $inclusion ): ?>
													<div class="roommaster-child__info">
														<span class="info-icon">
															<i class="aficon aficon-checkmark"></i>
														</span>
														<span class="info-text"><?php echo esc_html( $inclusion ); ?></span>
													</div>
												<?php endforeach; ?>
											</div>
										<?php endif; ?>

										<?php if ( $room_type->get( 'rate_policies' ) ): ?>
											<div class="roommaster-child__item">
												<span class="roommaster-child__bucketspan"><?php esc_html_e( 'Policies', 'awebooking' ); ?></span>
												<?php foreach ( $room_type->get( 'rate_policies' ) as $policy ): ?>
													<div class="roommaster-child__info">
														<span class="info-icon">
															<i class="aficon aficon-checkmark"></i>
														</span>
														<span><?php echo esc_html( $policy ); ?></span>
													</div>
												<?php endforeach; ?>
											</div>
										<?php endif; ?>
									</td>
									<td class="roommaster-occupancy">
										<?php if ( $room_type->get( 'number_adults' ) ) : ?>
											<span class="roommaster-occupancy__item">
												<?php
													/* translators: %1$s number adults, %2$s adult button */
													printf( esc_html_x( '%1$s x %2$s', 'number adults', 'awebooking' ),
														absint( $room_type->get( 'number_adults' ) ),
														'<i class="aficon aficon-man"></i><span class="screen-reader-text">' . esc_html_x( 'Adult', 'adult button', 'awebooking' ) . '</span>'
													);
												?>
											</span>
										<?php endif; ?>

										<?php if ( $room_type->get( 'number_children' ) ) : ?>
											<span class="roommaster-occupancy__item">
												<?php
													/* translators: %1$s number children, %2$s child button */
													printf( esc_html_x( '%1$s x %2$s', 'number children', 'awebooking' ),
														absint( $room_type->get( 'number_children' ) ),
														'<i class="aficon aficon-body"></i><span class="screen-reader-text">' . esc_html_x( 'Child', 'child button', 'awebooking' ) . '</span>'
													);
												?>

											</span>
										<?php endif; ?>

										<?php if ( $room_type->get( 'number_infants' ) ) : ?>
											<span class="roommaster-occupancy__item">
												<?php
													/* translators: %1$s number infants, %2$s infant button */
													printf( esc_html_x( '%1$s x %2$s', 'number infants', 'awebooking' ),
														absint( $room_type->get( 'number_infants' ) ),
														'<i class="aficon aficon-infant"></i><span class="screen-reader-text">' . esc_html_x( 'Infant', 'infant button', 'awebooking' ) . '</span>'
													);
												?>
											</span>
										<?php endif; ?>

										<div>
											(Tool tip)
											Maximum occupancy: 4
											Adults: 2
											Children: 2
										</div>
									</td>
									<td class="roommaster-inventory">
										<?php
										switch ( abrs_get_option( 'display_price', 'total' ) ) {
											case 'total':
												abrs_price( $room_rate->get_rate() );
												/* translators: %s nights */
												printf( esc_html_x( 'Cost for %s', 'total cost', 'awebooking' ),
													abrs_format_night_counts( $room_rate->timespan->nights() )
												); // WPCS: xss ok.
												break;

											case 'average':
												abrs_price( $room_rate->get_price( 'rate_average' ) );
												esc_html_e( 'Average cost per night', 'awebooking' );
												break;

											case 'first_night':
												abrs_price( $room_rate->get_price( 'rate_first_night' ) );
												esc_html_e( 'Cost for first night', 'awebooking' );
												break;
										}
										?>
									</td>

									<td class="roommaster-button">
										<?php if ( ! $room_rate->has_error() ) : ?>
											<?php
											abrs_bookroom_button([
												'room_type'   => $room_type->get_id(),
												'show_button' => true,
												'button_atts' => [
													'class' => 'booknow button is-primary',
												],
											]);
											?>
										<?php endif ?>

										<span class="roommaster-button__remaining-rooms">
											<?php
											$rooms_left = $remain_rooms->count();

											if ( $rooms_left <= 2 ) {
												/* translators: %s Number of remain rooms */
												printf( esc_html( _nx( 'Only %s room left', 'Only %s rooms left', $rooms_left, 'remain rooms', 'awebooking' ) ), esc_html( number_format_i18n( $rooms_left ) ) );
											} else {
												/* translators: %s Number of remain rooms */
												printf( esc_html_x( '%s rooms left', 'remain rooms', 'awebooking' ), esc_html( number_format_i18n( $rooms_left ) ) );
											}
											?>
										</span>
									</td>
								</tr>
								<?php //endforeach; ?>

							</tbody>
						</table>
					</div>
				</div>
			</div>

		</div>

		<div class="roommaster-detail">
			<div class="columns">
				<div class="column-3">
					<?php
					if ( has_post_thumbnail( $room_type->get_id() ) ) {
						echo get_the_post_thumbnail( $room_type->get_id(), 'awebooking_archive' );
					}
					?>
				</div>

				<div class="column-9">
					<div class="roommaster-tab tabs-main">
						<ul class="roommaster-tab__list tabs-main-list">
							<li class="active" rel="short-description-<?php echo absint( $room_type->get_id() ); ?>">
								<?php esc_html_e( 'Short description', 'awebooking' ); ?>
							</li>
							<li rel="amenities-<?php echo absint( $room_type->get_id() ); ?>">
								<?php esc_html_e( 'Amenities', 'awebooking' ); ?>
							</li>
							<div class="tabs-active-divider"></div>
						</ul>

						<div class="roommaster-tab__container tabs-main-container">
							<div id="short-description-<?php echo absint( $room_type->get_id() ); ?>" class="tabs-main-content">
								<?php echo esc_html( $room_type->get( 'short_description' ) ); ?>
							</div>
							<div id="amenities-<?php echo absint( $room_type->get_id() ); ?>" class="tabs-main-content">
								<?php $amenities = wp_get_post_terms( $room_type->get_id(), 'hotel_amenity' ); ?>
								<?php foreach ( $amenities as $amenity ) : ?>
									<div class="room-amenity">
										<?php if ( $icon = get_term_meta( $amenity->term_id, '_icon', true ) ) : ?>
											<span class="room-amenity__icon">
												<?php if ( 'svg' === $icon['type'] || 'image' === $icon['type'] ) : ?>
													<?php echo wp_get_attachment_image( $icon['icon'] ); ?>
												<?php else : ?>
													<i class="<?php echo esc_attr( $icon['type'] . ' ' . $icon['icon'] ); ?>"></i>
												<?php endif; ?>
											</span>
										<?php else : ?>
											<i class="aficon aficon-checkmark"></i>
										<?php endif; ?>
										<span class="room-amenity__title"><?php echo esc_html( $amenity->name ); ?></span>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	(function($) {
		$('.tabs-main').each(function() {
			var self= $(this),
				container = $('.tabs-main-container', self),
				list = $('.tabs-main-list', self),
				content = $('.tabs-main-content', self),
				divider = $('.tabs-active-divider', self);
				offsetThis = self.offset().left;

			list.find('li').click(function() {
				if(!$(this).hasClass('active')) {
			        list.find('li').removeClass("active");
			        $(this).addClass("active");
			        var width = $(this).outerWidth();
			        var offsetX = $(this).offset().left - offsetThis;
			        var index = $(this).index();

			        console.log(offsetX);

			        container.css({
			        	'transform': 'translateX('+(-(index*100))+'%)'
			        });

			        divider.css({
			        	'width': width,
			        	'left': offsetX
			        })
			    }
			});

		    var activeTabs = list.find('li.active');
		    var width = activeTabs.outerWidth();
			var offsetX = activeTabs.offset().left - offsetThis;
			var index = activeTabs.index();
		    container.css({
	        	'transform': 'translateX('+(-(index*100))+'%)'
	        });

	        divider.css({
	        	'width': width,
	        	'left': offsetX
	        })

		});


	})(jQuery);
</script>
