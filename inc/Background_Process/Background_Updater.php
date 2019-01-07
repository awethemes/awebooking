<?php

namespace AweBooking\Background_Process;

use AweBooking\Constants;
use AweBooking\Installer;
use Psr\Log\LoggerInterface;

class Background_Updater extends Background_Process {
	/**
	 * The installer instance.
	 *
	 * @var \AweBooking\Installer
	 */
	protected $installer;

	/**
	 * Constructor.
	 *
	 * @param Installer       $installer The installer instance.
	 * @param LoggerInterface $logger    The logger implementation.
	 */
	public function __construct( Installer $installer, LoggerInterface $logger ) {
		$this->installer = $installer;

		parent::__construct( $logger );
	}

	/**
	 * {@inheritdoc}
	 */
	public function dispatch() {
		$dispatched = parent::dispatch();

		if ( is_wp_error( $dispatched ) ) {
			$this->logger->error( sprintf( 'Unable to dispatch AweBooking updater: %s', $dispatched->get_error_message() ), [ 'source' => 'db_updates' ] );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function task( $callback ) {
		Constants::define( 'AWEBOOKING_UPDATING', true );

		// Include update functions.
		require_once dirname( __DIR__ ) . '/Core/updates.php';

		$result = false;

		if ( is_callable( $callback ) ) {
			$this->logger->info( sprintf( 'Running %s callback', $callback ), [ 'source' => 'db_updates' ] );

			$result = abrs_rescue( function () use ( $callback ) {
				return (bool) $this->installer->get_plugin()->call( $callback, [ $this->installer ] );
			}, false );

			if ( $result ) {
				$this->logger->info( sprintf( '%s callback needs to run again', $callback ), [ 'source' => 'db_updates' ] );
			} else {
				$this->logger->info( sprintf( 'Finished running %s callback', $callback ), [ 'source' => 'db_updates' ] );
			}
		} else {
			$this->logger->notice( sprintf( 'Could not find %s callback', $callback ), array( 'source' => 'db_updates' ) );
		}

		return $result ? $callback : false;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function complete() {
		$this->logger->info( 'Data update complete', array( 'source' => 'db_updates' ) );

		$this->installer->update_db_version();

		parent::complete();
	}
}
