<?php
namespace AweBooking\Admin\Controllers;

use Awethemes\Http\Request;
use AweBooking\Admin\Forms\New_Tax_Form;
use AweBooking\Reservation\Source\Store;
use AweBooking\Model\Tax;

class Tax_Controller extends Controller {
	/**
	 * The Store instance.
	 *
	 * @var \AweBooking\Reservation\Source\Store
	 */
	protected $store;

	/**
	 * The fallback redirect back.
	 *
	 * @var string
	 */
	protected $fallback;

	/**
	 * Constructor.
	 *
	 * @param \AweBooking\Reservation\Source\Store $store The Store instance.
	 */
	public function __construct( Store $store ) {
		$this->store = $store;

		$this->fallback = admin_url( 'admin.php?page=awebooking-settings&tab=reservation&section=reservation_tax' );
	}

	/**
	 * Show create form the settings.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function create( Request $request ) {
		$controls = new New_Tax_Form;

		return $this->response_view( 'taxes/create.php', compact( 'controls' ) );
	}

	/**
	 * Handle store the settings.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function store( Request $request ) {
		$request->verify_nonce( '_wpnonce', 'create_tax' );

		try {
			$input = ( new New_Tax_Form )->handle( $request->all() );
		} catch ( \Exception $e ) {
			$this->notices()->error( $e->getMessage() );
			return $this->redirect()->back( $this->fallback );
		}

		$tax = new Tax;

		$tax->fill( $input )->save();
		$saved = $tax->save();

		if ( $saved ) {
			$this->notices()->success( esc_html__( 'Added new tax / fee successfully!', 'awebooking' ) );
		} else {
			$this->notices()->warning( esc_html__( 'Error when add new tax / fee', 'awebooking' ) );
		}

		return $this->redirect()->to( $this->fallback );
	}

	/**
	 * Show update form the settings.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function show( Request $request, Tax $tax ) {
		$controls = new New_Tax_Form;

		$controls['name']->set_value( $tax->name );
		$controls['type']->set_value( $tax->type );
		$controls['code']->set_value( $tax->code );
		$controls['category']->set_value( $tax->category );
		$controls['amount_type']->set_value( $tax->amount_type );
		$controls['amount']->set_value( $tax->amount );

		return $this->response_view( 'taxes/show.php', compact( 'tax', 'controls' ) );
	}

	/**
	 * Handle update the settings.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function update( Request $request, Tax $tax ) {
		$request->verify_nonce( '_wpnonce', 'update_tax' );

		try {
			$input = ( new New_Tax_Form )->handle( $request->all() );
		} catch ( \Exception $e ) {
			$this->notices()->error( $e->getMessage() );
			return $this->redirect()->back( $this->fallback );
		}

		$tax->fill( $input )->save();

		$saved = $tax->save();

		if ( $saved ) {
			$this->notices()->success( esc_html__( 'Updated the tax / fee successfully!', 'awebooking' ) );
		} else {
			$this->notices()->warning( esc_html__( 'Error when the update tax / fee', 'awebooking' ) );
		}

		return $this->redirect()->to( $this->fallback );
	}

	/**
	 * Handle delete the settings.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function delete( Request $request, Tax $tax ) {
		$deleted = $tax->delete();

		if ( $deleted ) {
			$this->notices()->success( esc_html__( 'Deleted the tax / fee successfully!', 'awebooking' ) );
		} else {
			$this->notices()->warning( esc_html__( 'Error when the delete tax / fee', 'awebooking' ) );
		}

		return $this->redirect()->to( $this->fallback );
	}
}
