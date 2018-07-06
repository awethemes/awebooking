<?php
/**
 * Email styles.
 *
 * This template can be overridden by copying it to {yourtheme}/awebooking/emails/styles.php.
 *
 * @see      http://docs.awethemes.com/awebooking/developers/theme-developers/
 * @author   awethemes
 * @package  AweBooking
 * @version  3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// @codingStandardsIgnoreStart
$main_color       = abrs_get_option( 'email_base_color' )      ?: '#2196f3';
$text_color       = abrs_get_option( 'email_body_text_color' ) ?: '#74787E';
$body_bg_color    = abrs_get_option( 'email_body_bg_color' )   ?: '#f7f7f7';
$content_bg_color = abrs_get_option( 'email_bg_color' )        ?: '#ffffff';
// @codingStandardsIgnoreEnd

?>

/* Base */
body,
body *:not(html):not(style):not(br):not(tr):not(code) {
	font-family: Avenir, Helvetica, sans-serif;
	box-sizing: border-box;
}

body {
	font-size: 14px;
	color: <?php echo esc_attr( $text_color ); ?>;
	height: 100%;
	hyphens: auto;
	line-height: 1.4;
	margin: 0;
	-moz-hyphens: auto;
	-ms-word-break: break-all;
	width: 100% !important;
	-webkit-hyphens: auto;
	-webkit-text-size-adjust: none;
	word-break: break-all;
	word-break: break-word;
}

p,
ul,
ol,
blockquote {
	line-height: 1.4;
	text-align: left;
}

a {
	color: #3869D4;
	text-decoration: none;
}

a img {
	border: none;
}

/* Typography */
h1 {
	color: #444;
	font-size: 19px;
	font-weight: bold;
	margin-top: 0;
	text-align: left;
}

h2 {
	color: #444;
	font-size: 16px;
	font-weight: bold;
	margin-top: 0;
	text-align: left;
}

h3 {
	color: #444;
	font-size: 14px;
	font-weight: bold;
	margin-top: 0;
	text-align: left;
}

p {
	color: <?php echo esc_attr( $text_color ); ?>;
	font-size: 14px;
	line-height: 1.5em;
	margin-top: 0;
	text-align: left;
}

p.sub {
	font-size: 12px;
}

img {
	max-width: 100%;
}

blockquote {
  margin: 25px 0;
  border-left: solid 5px gray;
  text-align: left;
  padding: 10px 15px;
}

/* Helper */
.align-left {
	text-align: left;
}

.align-right {
	text-align: right;
}

.align-center {
	text-align: center;
}

/* Layout */
.wrapper {
	background-color: <?php echo esc_attr( $body_bg_color ); ?>;
	margin: 0;
	padding: 0;
	width: 100%;
	-premailer-cellpadding: 0;
	-premailer-cellspacing: 0;
	-premailer-width: 100%;
}

.content {
	margin: 0;
	padding: 0;
	width: 100%;
	-premailer-cellpadding: 0;
	-premailer-cellspacing: 0;
	-premailer-width: 100%;
}

/* Header */
.inner-header {
	padding: 25px 0;
	text-align: left;
}

.header a {
	color: <?php echo esc_attr( $main_color ); ?>;
	font-size: 19px;
	font-weight: bold;
	text-decoration: none;
	text-shadow: 0 1px 0 white;
}

/* Body */
.body {
	margin: 0;
	padding: 0;
	width: 100%;
	-premailer-cellpadding: 0;
	-premailer-cellspacing: 0;
	-premailer-width: 100%;
}

.inner-body {
	background-color: <?php echo esc_attr( $content_bg_color ); ?>;
	box-shadow: 0 2px 15px rgba(0, 0, 0, .05);
	margin: 0 auto;
	padding: 0;
	width: 570px;
	-premailer-cellpadding: 0;
	-premailer-cellspacing: 0;
	-premailer-width: 570px;
}

/* Subcopy */
.subcopy {
	border-top: 1px solid #EDEFF2;
	margin-top: 25px;
	padding-top: 25px;
}

.subcopy p {
	font-size: 12px;
}

.subcopy p:last-child {
	margin-bottom: 0;
}

/* Footer */
.footer {
	margin: 0 auto;
	padding: 0;
	text-align: center;
	width: 570px;
	-premailer-cellpadding: 0;
	-premailer-cellspacing: 0;
	-premailer-width: 570px;
}

.footer p {
	color: #AEAEAE;
	font-size: 12px;
	text-align: center;
}

/* Tables */
.table {
	margin: 30px auto;
	width: 100%;
	-premailer-cellpadding: 0;
	-premailer-cellspacing: 0;
	-premailer-width: 100%;
}

.table th {
	border-bottom: 1px solid #EDEFF2;
	padding-bottom: 8px;
}

.table td {
	color: <?php echo esc_attr( $text_color ); ?>;
	font-size: 15px;
	line-height: 18px;
	padding: 10px 0;
}

.content-cell {
	padding: 35px;
}

.content-cell p:last-child {
	margin-bottom: 0;
}

/* Buttons */
.action {
	margin: 30px auto;
	padding: 0;
	text-align: center;
	width: 100%;
	-premailer-cellpadding: 0;
	-premailer-cellspacing: 0;
	-premailer-width: 100%;
}

.button {
	border-radius: 3px;
	box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16);
	color: #FFF;
	display: inline-block;
	text-decoration: none;
	-webkit-text-size-adjust: none;
}

.button-blue {
	background-color: <?php echo esc_attr( $main_color ); ?>;
	border-top: 10px solid <?php echo esc_attr( $main_color ); ?>;
	border-right: 18px solid <?php echo esc_attr( $main_color ); ?>;
	border-bottom: 10px solid <?php echo esc_attr( $main_color ); ?>;
	border-left: 18px solid <?php echo esc_attr( $main_color ); ?>;
}

.button-green {
	background-color: #2ab27b;
	border-top: 10px solid #2ab27b;
	border-right: 18px solid #2ab27b;
	border-bottom: 10px solid #2ab27b;
	border-left: 18px solid #2ab27b;
}

.button-red {
	background-color: #e64539;
	border-top: 10px solid #e64539;
	border-right: 18px solid #e64539;
	border-bottom: 10px solid #e64539;
	border-left: 18px solid #e64539;
}

/* Panels */
.panel {
	margin: 0 0 21px;
}

.panel-content {
	background-color: #EDEFF2;
	padding: 16px;
}

.panel-item {
	padding: 0;
}

.panel-item p:last-child {
	margin-bottom: 0;
	padding-bottom: 0;
}

/* Promotions */
.promotion {
	background-color: #FFFFFF;
	border: 2px dashed #9BA2AB;
	margin: 0;
	margin-bottom: 25px;
	margin-top: 25px;
	padding: 24px;
	width: 100%;
	-premailer-cellpadding: 0;
	-premailer-cellspacing: 0;
	-premailer-width: 100%;
}

.promotion h1 {
	text-align: center;
}

.promotion p {
	font-size: 15px;
	text-align: center;
}

/* Custom CSS */
.body-content {
	margin-bottom: 25px;
	padding-bottom: 25px;
	border-bottom: solid 1px #eee;
}

.line-hotel-address {
	padding-right: 10px;
	border-right: solid 3px <?php echo esc_attr( $main_color ); ?>;
}

.booking-id {
	margin-top: 5px;
	padding: 5px 20px;
	border: dashed 2px #9BA2AB;
	display: inline-block;
	text-align: center;
	border-radius: 2px;
}

/* Table CSS */
.table-hotel,
.table-customer,
.table-booking,
.table-booking-totals {
	border-collapse: collapse;
}

.table-hotel,
.table-customer {
	margin-bottom: 25px;
}

.table-hotel td {
	vertical-align: top;
}

.table-customer th,
.table-customer td,
.table-booking th,
.table-booking td,
.table-booking-totals th,
.table-booking-totals td {
	font-size: 13px;
	text-align: left;
	padding: 7px 10px;
	vertical-align: top;
	box-sizing: border-box;
	border: solid 1px #eee;
}

.table-customer th {
	width: 150px;
}


.table-booking-totals {
	width: 50%;
	float: right;
}

.table-booking-totals th {
	background: #fefefe;
}

.table-booking-totals .awebooking-price__amount {
	font-size: 14px;
	font-weight: 700;
}

td.total-column,
th.total-column {
	width: 80px;
	text-align: right !important;
}
