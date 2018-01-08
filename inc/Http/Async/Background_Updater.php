<?php
namespace AweBooking\Http\Async;

use WP_Background_Process;
use Psr\Log\LoggerInterface;

class Background_Updater extends WP_Background_Process {
	/**
	 * The logger implementation.
	 *
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $logger;

	/**
	 * The action prefix.
	 *
	 * @var string
	 */
	protected $prefix = 'awebooking';

	/**
	 * The action name.
	 *
	 * @var string
	 */
	protected $action = 'awebooking_updater';

	/**
	 * Initiate new background process
	 *
	 * @param LoggerInterface $logger The logger implementation.
	 */
	public function __construct( LoggerInterface $logger ) {
		$this->logger = $logger;

		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	public function dispatch() {
		$dispatched = parent::dispatch();

		if ( is_wp_error( $dispatched ) ) {
			$this->logger->error(
				sprintf( 'Unable to dispatch AweBooking updater: %s', $dispatched->get_error_message() ),
				array( 'source' => 'db_updates' )
			);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function task( $callback ) {
		if ( ! defined( 'AWEBOOKING_UPDATING' ) ) {
			define( 'AWEBOOKING_UPDATING', true );
		}

		require_once trailingslashit( __DIR__ ) . '/../../update-functions.php';

		if ( is_callable( $callback ) ) {
			$this->logger->info( sprintf( 'Running %s callback', $callback ), array( 'source' => 'db_updates' ) );
			awebooking()->call( $callback );
			$this->logger->info( sprintf( 'Finished %s callback', $callback ), array( 'source' => 'db_updates' ) );
		} else {
			$this->logger->notice( sprintf( 'Could not find %s callback', $callback ), array( 'source' => 'db_updates' ) );
		}

		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function complete() {
		parent::complete();

		$this->logger->info( 'Data update complete', array( 'source' => 'db_updates' ) );
	}
}
