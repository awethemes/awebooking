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
						<div class="roommaster-info__title">Loại phòng</div>
						<div class="roommaster-info__image">
							<a href="#">
								<?php
									if ( has_post_thumbnail( $room_type->get_id() ) ) {
										echo get_the_post_thumbnail( $room_type->get_id(), 'awebooking_archive' );
									}
								?>
								<img src="https://picsum.photos/200/200" alt="">
								<span>Hình ảnh và chi tiết khác</span>
							</a>
						</div>
						<ul class="roommaster-info__list">
							<li class="info-item">
								<span class="info-icon">
									<i class="afc afc-building-alt"></i>
								</span>
								Hướng phòng: Đường phố
							</li>
							<li class="info-item">
								<span class="info-icon">
									<i class="afc afc-elevator"></i>
								</span>
								35 m²/377 ft²
							</li>
							<li class="info-item">
								<span class="info-icon">
									<i class="afc afc-shower"></i>
								</span>
								vòi hoa sen
							</li>
							<li class="info-item">
								<span class="info-icon">
									<i class="afc afc-bed"></i>
								</span>
								3 giường đôi
							</li>
						</ul>
					</div>
				</div>
				<div class="column-9">
					<div class="roommaster-list">
						<table class="roommaster-table">
							<thead>
								<tr>
									<th class="thead-1">Lựa chọn</th>
									<th class="thead-2">Sức chứa</th>
									<th class="thead-3">Giá phòng/đêm</th>
									<th class="thead-4">SL</th>
									<th class="thead-5">Đặt nhiều nhất</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="roommaster-child">
										<div class="roommaster-child__item">
											<span class="roommaster-child__bucketspan">Lợi ích</span>
											<div class="roommaster-child__info">
												<span class="info-icon">
													<i class="afc afc-dogs-not-permitted"></i>
												</span>
												<span class="info-text">Chỉ thanh toán vào ngày 19 Tháng Sáu 2018</span>
											</div>
											<div class="roommaster-child__info">
												<span class="info-icon">
													<i class="afc afc-dogs-not-permitted"></i>
												</span>
												<span>Chỉ thanh toán vào ngày 19 Tháng Sáu 2018</span>
											</div>
										</div>
										<div class="roommaster-child__item">
											<span class="roommaster-child__bucketspan">Giảm giá</span>
											<div class="roommaster-child__info">
												<span class="info-icon">
													<i class="afc afc-dogs-not-permitted"></i>
												</span>
												<span>Coupon giảm giá: 73.202 ₫</span>
											</div>
										</div>
									</td>
									<td class="roommaster-occupancy">
										<span class="roommaster-occupancy__item">
											<span>6</span>
											x
											<i class="afc afc-male"></i>
										</span>

										<span class="roommaster-occupancy__item">
											<span>3</span>
											x
											<i class="afc afc-child"></i>
										</span>
									</td>
									<td  class="roommaster-inventory">
										<?php
										switch ( abrs_get_option( 'display_price', 'total' ) ) {
											case 'total':
												abrs_price( $room_rate->get_rate() );
												echo sprintf( 'Cost for %s nights', $room_rate->timespan->nights() );
												break;

											case 'first_night':
												break;
										}
										?>
									</td>
									<td class="roommaster-select">
										<select name="" id="" class="select-form">
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
										</select>
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
								<tr>
									<td class="roommaster-child">
										<div class="roommaster-child__item">
											<span class="roommaster-child__bucketspan">Lợi ích</span>
											<div class="roommaster-child__info">
												<span class="info-icon">
													<i class="afc afc-dogs-not-permitted"></i>
												</span>
												<span class="info-text">Chỉ thanh toán vào ngày 19 Tháng Sáu 2018</span>
											</div>
											<div class="roommaster-child__info">
												<span class="info-icon">
													<i class="afc afc-dogs-not-permitted"></i>
												</span>
												<span>Chỉ thanh toán vào ngày 19 Tháng Sáu 2018</span>
											</div>
										</div>
										<div class="roommaster-child__item">
											<span class="roommaster-child__bucketspan">Giảm giá</span>
											<div class="roommaster-child__info">
												<span class="info-icon">
													<i class="afc afc-dogs-not-permitted"></i>
												</span>
												<span>Coupon giảm giá: 73.202 ₫</span>
											</div>
										</div>
									</td>
									<td class="roommaster-occupancy">
										<span class="roommaster-occupancy__item">
											<span>6</span>
											x
											<i class="afc afc-male"></i>
										</span>

										<span class="roommaster-occupancy__item">
											<span>3</span>
											x
											<i class="afc afc-child"></i>
										</span>
									</td>
									<td  class="roommaster-inventory">
										<?php
										switch ( abrs_get_option( 'display_price', 'total' ) ) {
											case 'total':
												abrs_price( $room_rate->get_rate() );
												echo sprintf( 'Cost for %s nights', $room_rate->timespan->nights() );
												break;

											case 'first_night':
												break;
										}
										?>
									</td>
									<td class="roommaster-select">
										<select name="" id="" class="select-form">
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
										</select>
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

										<span class="abroom__remaining-rooms">
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
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
		</div>

		<div class=" roommaster-detail">
			<div class="columns">
				<div class="column-3">
					<img src="https://picsum.photos/500/500" alt="">
				</div>
				
				<div class="column-9">
					<div class="roommaster-tab tabs-main">
						<ul class="roommaster-tab__list tabs-main-list">
							<li class="active" rel="tab1">
								Tab 1
							</li>
							<li rel="tab2">
								Tab 2
							</li>
							<li rel="tab3">
								Tab 3
							</li>
							<li rel="tab4">
								Tab 4
							</li>
							<div class="tabs-active-divider"></div>
						</ul>
						<div class="roommaster-tab__container tabs-main-container">
							<div id="tab1" class="tabs-main-content">
								<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veniam doloribus autem modi praesentium blanditiis provident molestiae cumque possimus, enim quas, incidunt fuga a inventore facere. Expedita molestias quidem nisi cum?</p>
							</div>
							<div id="tab2" class="tabs-main-content">
								<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veniam doloribus autem modi praesentium blanditiis provident molestiae cumque possimus, enim quas, incidunt fuga a inventore facere. Expedita molestias quidem nisi cum?</p>
							</div>
							<div id="tab3" class="tabs-main-content">
								<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veniam doloribus autem modi praesentium blanditiis provident molestiae cumque possimus, enim quas, incidunt fuga a inventore facere. Expedita molestias quidem nisi cum?</p>
							</div>
							<div id="tab4" class="tabs-main-content">
								<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veniam doloribus autem modi praesentium blanditiis provident molestiae cumque possimus, enim quas, incidunt fuga a inventore facere. Expedita molestias quidem nisi cum?</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="box abroom">
	<div class="abroom__data">
		<table class="abroom__table">
			<tbody>
				<tr>
					<td class="column-room-info">
						<div class="clearfix">
							<div class="abroom__image">
								<?php
								if ( has_post_thumbnail( $room_type->get_id() ) ) {
									echo get_the_post_thumbnail( $room_type->get_id(), 'awebooking_archive' );
								}
								?>
							</div>

							<div class="abroom__room">
								<h2 class="abroom__roomname"><a href="<?php echo esc_url( get_permalink( $room_type->get_id() ) ); ?>" rel="bookmark" target="_blank"><?php echo esc_html( $room_type->get( 'title' ) ); ?></a></h2>

								<div class="abroom__occupancy">
									<?php
									$max_capacity = $room_type->get( 'maximum_occupancy' );

									/* translators: %s Maximum capacity */
									echo sprintf( esc_html( _nx( 'Maximum capacity for %s person', 'Maximum capacity for %s people', $max_capacity, 'awebooking' ) ), esc_html( number_format_i18n( $max_capacity ) ) );
									?>
								</div>

								<div>
									<strong><?php esc_html_e( 'What\'s included', 'awebooking' ); ?></strong>

									<?php if ( ! empty( $room_type['rate_inclusions'] ) ) : ?>
										<?php foreach ( $room_type->get( 'rate_inclusions' ) as $string ) : ?>

											<p><?php echo abrs_esc_text( $string ); // WPCS: XSS OK. ?></p>

										<?php endforeach ?>
									<?php endif ?>
								</div>

							</div>
						</div>
					</td>

					<td class="column-room-inventory">
						<div class="abroom__inventory">
							<?php
							switch ( abrs_get_option( 'display_price', 'total' ) ) {
								case 'total':
									abrs_price( $room_rate->get_rate() );
									echo sprintf( 'Cost for %s nights', $room_rate->timespan->nights() );
									break;

								case 'first_night':
									break;
							}
							?>
						</div>
					</td>

					<td class="column-room-button">
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

						<span class="abroom__remaining-rooms">
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
			</tbody>
		</table>
	</div>

	<div class="abroom__details">

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
