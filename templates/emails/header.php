<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
</head>
<body>
	<style>
		@media only screen and (max-width: 600px) {
			.inner-body {
				width: 100% !important;
			}

			.inner-header {
				width: 100% !important;
			}

			.inner-footer {
				width: 100% !important;
			}
		}
	</style>

	<table class="wrapper" width="100%" cellpadding="0" cellspacing="0">
		<tr>
			<td align="center">
				<table class="content" width="100%" cellpadding="0" cellspacing="0">
					<tr>
						<td class="header">
							<table class="inner-header" align="center" width="570" cellpadding="0" cellspacing="0">
								<tr>
									<td align="left">
										<a href="<?php echo esc_url( apply_filters( 'abrs_email_header_url', get_site_url() ) ); ?>" target="_blank">
											<?php if ( $img = apply_filters( 'abrs_email_header_image', abrs_get_option( 'email_header_image' ) ) ) : ?>
												<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" />
											<?php else : ?>
												<?php echo esc_html( apply_filters( 'abrs_email_header_text', get_bloginfo( 'name', 'display' ) ) ); ?>
											<?php endif ?>
										</a>
									</td>
								</tr>
							</table>
						</td>
					</tr>

					<tr>
						<td class="body" width="100%" cellpadding="0" cellspacing="0">
							<table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0">
								<tr>
									<td class="content-cell">
