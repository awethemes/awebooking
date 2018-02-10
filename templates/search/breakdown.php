			<div id="popup-rate-<?php echo absint( $room_type->get_id() ); ?>" class="breakdown-popup mfp-hide">
				<div class="awebooking-breakdown">
					<div class="awebooking-breakdown__wrapper">
						<div class="awebooking-breakdown__header">
							<h3><?php esc_html_e( 'Rate Informations', 'awebooking' ); ?></h3>
						</div>

						<div class="awebooking-breakdown__content">
							<h5 class="awebooking-breakdown__title"><?php echo esc_html( $room_type->get_title() ); ?></h5>

							<table class="table table-condensed awebooking-breakdown__table">
								<thead>
									<tr>
									<th>Date</th>
									<th>Per night</th>
									<th>Extra Adults Cost</th>
									<th>Extra Children Cost</th>
									</tr>
								</thead>

								<tbody>
									<tr>
										<td>Mon 11, Dec</td>
										<td><del>5500</del>400</td>
										<td>3300</td>
										<td>1100</td>
									</tr>

									<tr>
										<td>Mon 11, Dec</td>
										<td><del>5500</del>400</td>
										<td>3300</td>
										<td>1100</td>
									<tr>

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
