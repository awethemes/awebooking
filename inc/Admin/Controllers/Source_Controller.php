<?php
namespace AweBooking\Admin\Controllers;

use Skeleton\CMB2\CMB2;
use Awethemes\Http\Request;
use AweBooking\Setting;
use AweBooking\Admin\Admin_Settings;
use AweBooking\Admin\Forms\New_Source_Form;
use AweBooking\Admin\Forms\Edit_Source_Form;
use AweBooking\Source\Store;
use AweBooking\Model\Source;
use AweBooking\Support\Utils as U;
use AweBooking\Model\Tax;

class Source_Controller extends Controller {
	/**
	 * The Store instance.
	 *
	 * @var \AweBooking\Source\Store
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
	 * @param \AweBooking\Source\Store $store The Store instance.
	 */
	public function __construct( Store $store ) {
		$this->store = $store;

		$this->fallback = admin_url( 'admin.php?page=awebooking-settings&tab=reservation' );
	}

	/**
	 * Handle store the settings.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function store( Request $request ) {
		$request->verify_nonce( '_wpnonce', 'create_reservation_source' );

		try {
			$input = ( new New_Source_Form )->handle( $request->all() );
		} catch ( \Exception $e ) {
			$this->notices()->error( $e->getMessage() );
			return $this->redirect()->back( $this->fallback );
		}

		// Perform create new reservation source.
		$inserted = $this->store->insert([
			'type' => 'direct',
			'uid'  => 'direct_' . sanitize_key( $input['new_source_name'] ),
			'name' => sanitize_text_field( wp_unslash( $input['new_source_name'] ) ),
		]);

		if ( $inserted ) {
			$this->notices()->success( esc_html__( 'Added new source successfully!', 'awebooking' ) );
		} else {
			$this->notices()->warning( esc_html__( 'Error when add source', 'awebooking' ) );
		}

		return $this->redirect()->back( $this->fallback );
	}

	/**
	 * Handle store the settings.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function update( Request $request, $source ) {
		$request->verify_nonce( '_wpnonce', 'update_source' );

		$source = awebooking( 'reservation_sources' )->get( $source );

		try {
			$input = ( new Edit_Source_Form )->handle( $request->all() );
		} catch ( \Exception $e ) {
			$this->notices()->error( $e->getMessage() );
			return $this->redirect()->back( $this->fallback );
		}

		$tax = new Tax( $input['tax_rates'] );

		$saved = $source->set_surcharge( $tax );

		// if ( $saved ) {
		// 	$this->notices()->success( esc_html__( 'Updated the source successfully!', 'awebooking' ) );
		// } else {
		// 	$this->notices()->warning( esc_html__( 'Error when the update source', 'awebooking' ) );
		// }

		return $this->redirect()->to( $this->fallback );
	}

	/**
	 * Handle store the settings.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function bulk_update( Request $request, Store $store ) {
		$request->verify_nonce( '_wpnonce', 'awebooking_reservation_source' );

		if ( ! $request->has( 'sources' ) ) {
			return $this->redirect()->back( $this->fallback );
		}

		foreach ( (array) $request->input( 'sources' ) as $key => $data_source ) {
			if ( empty( $data_source['uid'] ) && $key !== $data_source['uid'] ) {
				continue;
			}

			$store_source = $store->get( $data_source['uid'] );

			if ( is_null( $store_source ) ) {
				$this->perform_insert_source( $store, $data_source );
			} else {
				$this->perform_update_source( $store, $data_source );
			}
		}

		$this->notices()->success( esc_html__( 'Update sources successfully!', 'awebooking' ) );

		return $this->redirect()->back( $this->fallback );
	}

	/**
	 * [perform_insert_source description]
	 *
	 * @param  Store  $store  [description]
	 * @param  [type] $source [description]
	 * @return [type]
	 */
	protected function perform_insert_source( Store $store, $source ) {
		return $store->insert([
			'type'    => 'direct',
			'uid'     => $source['uid'],
			'name'    => isset( $source['name'] ) ? trim( sanitize_text_field( $source['name'] ) ) : '',
			'enabled' => $this->parse_source_status( $source ),
		]);
	}

	protected function perform_update_source( Store $store, $source ) {
		$store->update( $source['uid'], [
			'enabled' => $this->parse_source_status( $source ),
		]);
	}

	/**
	 * Parse the source status (enable/disable).
	 *
	 * @param  array $source The source args.
	 * @return boolean
	 */
	protected function parse_source_status( $source ) {
		if ( empty( $source['enabled'] ) ) {
			return false;
		}

		return in_array( $source['enabled'], [ '1', 'on', 'enable', 'true', true, 1 ] );
	}

	/**
	 * Show update form the settings.
	 *
	 * @param  \Awethemes\Http\Request $request The current request.
	 * @return \Awethemes\Http\Response
	 */
	public function show( Request $request, $source ) {
		$controls = new Edit_Source_Form;
		$source = awebooking( 'reservation_sources' )->get( $source );
		$controls['tax_rates']->set_value( $source->get_surcharge() );

		return $this->response_view( 'sources/show.php', compact( 'request', 'controls' ) );
	}
}
