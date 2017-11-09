<?php
namespace AweBooking;

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
		parent::__construct();

		$this->logger = $logger;
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param string $callback Update callback function.
	 * @return mixed
	 */
	protected function task( $callback ) {
		if ( ! defined( 'AWEBOOKING_UPDATING' ) ) {
			define( 'AWEBOOKING_UPDATING', true );
		}

		require_once trailingslashit( __DIR__ ) . 'update-functions.php';

		if ( is_callable( $callback ) ) {
			$logger->info( sprintf( 'Running %s callback', $callback ), array( 'source' => 'db_updates' ) );
			awebooking()->call( $callback );
			$logger->info( sprintf( 'Finished %s callback', $callback ), array( 'source' => 'db_updates' ) );
		} else {
			$logger->notice( sprintf( 'Could not find %s callback', $callback ), array( 'source' => 'db_updates' ) );
		}

		return false;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();

		$this->logger->info( 'Data update complete', array( 'source' => 'db_updates' ) );
	}

	/**
	 * Dispatch updater.
	 *
	 * Updater will still run via cron job if this fails for any reason.
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
	 * Is the updater running?
	 *
	 * @return boolean
	 */
	public function is_updating() {
		return false === $this->is_queue_empty();
	}
}
