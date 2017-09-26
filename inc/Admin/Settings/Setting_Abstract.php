<?php
namespace AweBooking\Admin\Settings;

use Skeleton\CMB2\CMB2;

abstract class Setting_Abstract {
	/**
	 * The main CMB2 (Skeleton) instance.
	 *
	 * @var CMB2
	 */
	protected $settings;

	/**
	 * Constructor the setting.
	 *
	 * @param CMB2 $settings The main CMB2 (Skeleton) instance.
	 */
	public function __construct( CMB2 $settings ) {
		$this->settings = $settings;

		$this->register();
	}

	/**
	 * Register sections, panels, fields,...
	 *
	 * @return void
	 */
	abstract public function register();
}
