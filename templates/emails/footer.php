									</td>
								</tr>
							</table>
						</td>
					</tr>

					<tr>
						<td class="footer">
							<table class="inner-footer" align="center" width="570" cellpadding="0" cellspacing="0">
								<tr>
									<td class="content-cell" align="center">
										<?php echo wp_kses_post( wpautop( wptexturize( apply_filters( 'abrs_email_footer_text', abrs_get_option( 'email_copyright' ) ) ) ) ); ?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

</body>
</html>
