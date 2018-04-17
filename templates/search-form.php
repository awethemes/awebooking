<?php

$form_classes = [];

?>
<form method="GET" action="<?php echo esc_url( arbs_page_permalink( 'search_results' ) ); ?>" class="<?php echo esc_attr( abrs_html_class( $form_classes ) ); ?>" role="search">

	<div class="searchbox">
		<div class="searchbox__wrapper">

			<div tabindex="0" class="IconBox IconBox--checkIn">
				<div class="IconBox__wrapper"><i class="ficon IconBox__icon ficon-20 ficon-check-in"></i>
					<div class="IconBox__child">
						<div data-selenium="checkInBox" data-date="2018-04-23" class="SearchBoxTextDescription SearchBoxTextDescription--checkIn">
							<div class="SearchBoxTextDescription__title" data-selenium="textInput">23 Apr 2018</div>
							<div class="SearchBoxTextDescription__desc">Monday</div>
						</div>
					</div>
				</div>
			</div>

			<div tabindex="0" class="IconBox IconBox--checkOut">
				<div class="IconBox__wrapper"><i class="ficon IconBox__icon ficon-20 ficon-check-out"></i>
					<div class="IconBox__child">
						<div data-selenium="checkOutBox" data-date="2018-04-24" class="SearchBoxTextDescription SearchBoxTextDescription--checkOut">
							<div class="SearchBoxTextDescription__title" data-selenium="textInput">24 Apr 2018</div>
							<div class="SearchBoxTextDescription__desc">Tuesday</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>

</form>
