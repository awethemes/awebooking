<?php
namespace AweBooking\Admin;

use Skeleton\Support\Priority_List;

class Admin_Welcome {
	/**
	 * Tabs collection to render.
	 *
	 * @var Priority_List
	 */
	protected $tabs;

	/**
	 * Admin welcome constructor.
	 */
	public function __construct() {
		$this->tabs = new Priority_List;
	}

	/**
	 * Handler add tab to admin-welcome.
	 *
	 * @param array $tab The tab to register.
	 */
	public function add_tab( array $tab ) {
		if ( ! isset( $tab['id'] ) ) {
			return $this;
		}

		$title = empty( $tab['title'] ) ? $tab['id'] : $tab['title'];
		$id = sanitize_key( $tab['id'] );

		$tab = wp_parse_args( $tab, array(
			'id'       => $id,
			'title'    => $title,
			'link'     => '#',
			'priority' => 10,
			// 'callback' => '',
		) );

		$this->tabs->insert( $id, $tab, $tab['priority'] );

		return $this;
	}

	/**
	 * Remove tab by key and priority.
	 *
	 * @param  string $id The tab to remove.
	 * @return $this
	 */
	public function remove_tab( $id ) {
		$this->tabs->remove( $id );

		return $this;
	}

	/**
	 * Get flaten of registerd tabs.
	 *
	 * @return array
	 */
	public function get_tabs() {
		return $this->tabs;
	}

	/**
	 * Render default welcome screen.
	 *
	 * @access private
	 */
	public function output() {
		global $current_tab;
		$current_tab = $this->get_current_tab();

		// Don't output anything if $tabs is empty.
		if ( ! $current_tab ) {
			return;
		}

		if ( isset( $current_tab['nowrap'] ) && true === $current_tab['nowrap'] && ! empty( $current_tab['callback'] ) ) {
			call_user_func( $current_tab['callback'] );
			return;
		}

		?><div class="wrap awethemes-welcome-page">
			<?php $this->display_nav_tabs( $current_tab['id'] ); ?>

			<?php if ( ! empty( $current_tab['callback'] ) ) : ?>
				<div class="awethemes-welcome-tab <?php echo esc_attr( $current_tab['id'] ); ?>">
					<?php call_user_func( $current_tab['callback'] ); ?>
				</div>
			<?php endif; ?>

		</div><?php
	}

	/**
	 * Display nav tabs
	 * We make it public so any extends code can be use this method.
	 *
	 * @param string $current Active current tab by key.
	 */
	public function display_nav_tabs( $current = null ) {
		$tabs = $this->get_tabs();
		$links = '';

		// Don't output anything if $tabs is empty.
		if ( empty( $tabs ) ) {
			return;
		}

		if ( is_null( $current ) ) {
			$current = $this->get_current_tab();
			$current = $current['id'];
		}

		// Build tabs.
		foreach ( $tabs as $id => $tab ) {
			if ( ! empty( $tab['callback'] ) ) {
				$tab['link'] = add_query_arg( 'tab', $id, admin_url( 'admin.php?page=awebooking' ) );
			}

			$active = ( $id == $current ) ? 'nav-tab-active' : '';
			$links  .= '<a class="nav-tab ' . $active . '" href="' . esc_url( $tab['link'] ) . '">' . esc_html( $tab['title'] ) . '</a>';
		}

		if ( $links ) {
			printf( '<h2 class="nav-tab-wrapper">%s</h2>', $links ); // WPCS: xss ok.
		}
	}

	/**
	 * Get current tab from $_REQUEST['tab'] otherewide
	 * use first key in registed tabs as current tab.
	 *
	 * @return array|null
	 */
	protected function get_current_tab() {
		$tabs = $this->get_tabs();

		if ( isset( $_REQUEST['tab'] ) ) {
			$request_tab = sanitize_text_field( wp_unslash( $_REQUEST['tab'] ) );
			$current_tab = $tabs->get( $request_tab );
		} else {
			// Don't known this break the chain.
			$a = $tabs->toArray();
			$current_tab = reset( $a );
		}

		return $current_tab;
	}
}
