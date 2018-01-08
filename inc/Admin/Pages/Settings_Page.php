<?php
namespace AweBooking\Admin\Pages;

use Skeleton\CMB2\Panel;
use Skeleton\CMB2\Section;
use AweBooking\Admin\Admin_Settings;
use AweBooking\Http\Routing\Url_Generator;
use AweBooking\Support\Utils as U;

class Settings_Page {
	/**
	 * The Setting instance.
	 *
	 * @var \AweBooking\Admin\Admin_Settings
	 */
	protected $settings;

	/**
	 * The Url_Generator instance.
	 *
	 * @var \AweBooking\Http\Routing\Url_Generator
	 */
	protected $url_generator;

	/**
	 * The current tab based on current request.
	 *
	 * @var Panel|Section
	 */
	protected $current_tab;

	/**
	 * The current section based on current request.
	 *
	 * @var Section|null
	 */
	protected $current_section;

	/**
	 * Constructor.
	 *
	 * @param Admin_Settings $settings      The Setting instance.
	 * @param Url_Generator  $url_generator The Url_Generator instance.
	 */
	public function __construct( Admin_Settings $settings, Url_Generator $url_generator ) {
		$this->settings = $settings;
		$this->url_generator = $url_generator;
	}

	/**
	 * Output the contents.
	 *
	 * @return void
	 */
	public function output() {
		$this->prepare_output();

		?><div id="awebooking-page-settings" class="wrap cmb2-options-page">
			<h1 class="wp-heading-inline screen-reader-text"><?php esc_html__( 'AweBooking Settings', 'awebooking' ); ?></h1>

			<?php $this->print_the_navigation(); ?>

			<form class="cmb-form" method="POST" action="<?php echo esc_url( $this->url_generator->admin_route( 'settings' ) ); ?>" enctype="multipart/form-data">
				<?php $this->print_the_fields(); ?>
			</form>
		</div><?php
	}

	/**
	 * Prepare output the fields.
	 *
	 * @return void
	 */
	protected function prepare_output() {
		$cmb2_render = $this->settings->get_render();
		$cmb2_render->navigation_class = 'wp-clearfix cmb2-nav-default';

		// Prepare the fields.
		$this->settings->prepare_controls();
		$this->settings->prepare_validation_errors();

		// Setup current tab and section from current request.
		$this->setup_tab_from_request();
	}

	/**
	 * Loops through and displays fields.
	 *
	 * @return void
	 */
	protected function print_the_fields() {
		// Don't output anything if current not provided.
		if ( ! $this->is_valid_request() ) {
			printf( '<p style="color: #ff5722;">%s</p>', esc_html__( 'You\'re going the wrong way!', 'awebooking' ) );
			return;
		}

		// Determines current section to display.
		$current_section = $this->current_section ? $this->current_section : $this->current_tab;

		$this->settings->render_form_open();
		echo '<input type="hidden" name="_wp_http_referer" value="' . esc_url( $this->url_generator->full() ) . '">';
		echo '<input type="hidden" name="_setting_section" value="' . esc_attr( $current_section->id ) . '">';

		// Print the sub-navigation in case current tab is a Panel.
		if ( $this->current_tab instanceof Panel ) {
			$this->print_panel_navigation( $this->current_tab );
		}

		$this->print_fields_in_section( $current_section );

		echo '<input type="submit" name="submit-cmb" value="' . esc_html__( 'Save changes', 'awebooking' ) . '" class="button-primary">';
		$this->settings->render_form_close();
	}

	/**
	 * Print fields in a section.
	 *
	 * @param  Section $section The Section to render.
	 * @return void
	 */
	protected function print_fields_in_section( Section $section ) {
		printf( '<div id="%1$s" class="cmb2-tab-pane active">', esc_attr( $section->uniqid() ) );

		foreach ( $section->fields as $field ) {
			$this->settings->render_field( $field );
		}

		print "\n</div>\n";
	}

	/**
	 * Print the panel navigation.
	 *
	 * @param  Panel $panel The panel to render.
	 * @return void
	 */
	protected function print_panel_navigation( Panel $panel ) {
		$current_section = $this->current_section ? $this->current_section->id : '';

		$navigation = [];
		foreach ( $panel->sections as $section ) {
			$navigation[] = sprintf( '<li><a href="%1$s" class="%3$s">%2$s</a></li>',
				esc_url( add_query_arg( [ 'tab' => $panel->id, 'section' => $section->id ], admin_url( 'admin.php?page=awebooking-settings' ) ) ),
				esc_html( $section->title ?: $section->id ),
				esc_attr( $current_section === $section->id ? 'current' : '' )
			);
		}

		// @codingStandardsIgnoreLine
		print( "\n<ul class=\"subsubsub\">\n\t" . implode( $navigation, "\n\t" ) . "\n</ul>" );
		print( "\n<div class=\"clear\"></div>\n" );
	}

	/**
	 * Print the navigation.
	 *
	 * @return void
	 */
	protected function print_the_navigation() {
		$current_tab = $this->current_tab ? $this->current_tab->id : '';
		$navigation  = [];

		foreach ( $this->settings->tabs() as $tabable ) {
			$navigation[] = sprintf( '<a href="%1$s" class="nav-tab%3$s">%4$s %2$s</a>',
				esc_url( add_query_arg( 'tab', $tabable->id, admin_url( 'admin.php?page=awebooking-settings' ) ) ),
				esc_html( $tabable->title ?: $tabable->id ),
				esc_attr( $current_tab === $tabable->id ? ' nav-tab-active' : '' ),
				$tabable->build_icon()
			);
		}

		// @codingStandardsIgnoreLine
		print( "\n<nav class=\"nav-tab-wrapper awebooking-nav-tab-wrapper\">\n\t" . implode( $navigation, "\n\t" ) . "\n</nav>\n" );
	}

	/**
	 * Get working section from request.
	 *
	 * @return void
	 */
	protected function setup_tab_from_request() {
		$all_tabs = U::collect( $this->settings->tabs() );

		// If current tab not provided in the request, use the first tab.
		if ( empty( $_REQUEST['tab'] ) ) {
			$this->current_tab = $all_tabs->first();
			return;
		}

		// Get the requested tab from current request.
		$requested_tab = sanitize_text_field( wp_unslash( $_REQUEST['tab'] ) );
		if ( ! $requested_tab || ! $all_tabs->has( $requested_tab ) ) {
			return;
		}

		// Set the current_tab.
		$this->current_tab = $all_tabs->get( $requested_tab );

		// If current tab is instanceof the Panel, continue setup the section.
		if ( $this->current_tab instanceof Panel ) {
			$panel_sections = U::collect( $this->current_tab->sections );

			// Use first section in case not see any in the request.
			if ( empty( $_REQUEST['section'] ) ) {
				$this->current_section = $panel_sections->first();
				return;
			}

			$requested_section = sanitize_text_field( wp_unslash( $_REQUEST['section'] ) );

			$this->current_section = $panel_sections->has( $requested_section )
				? $panel_sections->get( $requested_section )
				: null;
		}
	}

	/**
	 * Determines if current request is valid to render contents.
	 *
	 * @return boolean
	 */
	protected function is_valid_request() {
		if ( is_null( $this->current_tab ) ||
			( $this->current_tab instanceof Panel && ! $this->current_section ) ) {
			return false;
		}

		return true;
	}
}
