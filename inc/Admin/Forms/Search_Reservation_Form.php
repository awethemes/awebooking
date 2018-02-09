<?php
namespace AweBooking\Admin\Forms;

use AweBooking\Dropdown;
use AweBooking\Constants;

class Search_Reservation_Form extends Form_Abstract {
	/**
	 * Form ID.
	 *
	 * @var string
	 */
	protected $form_id = 'awebooking_search_reservation_from';

	/**
	 * The form layout, "minimal", "default".
	 *
	 * @var string
	 */
	protected $layout;

	/**
	 * Constructor.
	 *
	 * @param string $layout The form layout.
	 */
	public function __construct( $layout = 'default' ) {
		$this->layout = $layout;

		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function fields() {
		if ( 'minimal' !== $this->layout ) {
			$this->add_field([
				'id'          => 'reservation_source',
				'type'        => 'select',
				'name'        => esc_html__( 'Select source', 'awebooking' ),
				'validate'    => 'required',
				'options_cb'  => Dropdown::cb( 'get_reservation_sources' ),
			]);
		}

		$this->add_field([
			'id'          => 'check_in_out',
			'type'        => 'date_range',
			'name'        => esc_html__( 'Check-In and Check-Out', 'awebooking' ),
			'validate'    => 'date_period',
			'attributes'  => [ 'placeholder' => Constants::DATE_FORMAT ],
			'date_format' => Constants::DATE_FORMAT,
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function output() {
		$this->prepare_validate();

		$request = $this->get_request();

		if ( $request->has( 'check_in_out' ) ) {
			$this['check_in_out']->set_value( $request->get( 'check_in_out' ) );
		}

		if ( 'minimal' !== $this->layout && $request->has( 'reservation_source' ) ) {
			$this['reservation_source']->set_value( $request->get( 'reservation_source' ) );
		}

		?><div class="awebooking-row">
			<?php if ( 'minimal' !== $this->layout ) : ?>
				<div class="awebooking-column reservation_source_column">
					<?php print $this['reservation_source']->label(); // @wpcs: xss ok. ?>

					<?php $this['reservation_source']->render(); ?>

					<?php $this['reservation_source']->errors(); ?>
				</div>
			<?php endif ?>

			<div class="awebooking-column check_in_out_column">
				<?php print $this['check_in_out']->label(); // @wpcs: xss ok. ?>

				<?php $this['check_in_out']->render(); ?>

				<?php $this['check_in_out']->errors(); ?>
			</div>

			<div class="awebooking-column submit_column">
				<label>&nbsp;</label>

				<button class="button">
					<span class="dashicons dashicons-search"></span>
					<?php echo esc_html_x( 'Search', 'search availabilitsy', 'awebooking' ); ?>
				</button>
			</div>
		</div><?php // @codingStandardsIgnoreLine
	}
}
