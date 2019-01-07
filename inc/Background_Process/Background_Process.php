<?php

namespace AweBooking\Background_Process;

use Psr\Log\LoggerInterface;

abstract class Background_Process extends \WP_Background_Process {
	/**
	 * The background process prefix.
	 *
	 * @var string
	 */
	protected $prefix = 'awebooking';

	/**
	 * The logger implementation.
	 *
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $logger;

	/**
	 * Constructor.
	 *
	 * @param LoggerInterface $logger The logger implementation.
	 */
	public function __construct( LoggerInterface $logger ) {
		$this->logger = $logger;

		parent::__construct();
	}

	/**
	 * Delete all batches.
	 *
	 * @return $this
	 */
	public function delete_all_batches() {
		global $wpdb;

		$table  = $wpdb->options;
		$column = 'option_name';

		if ( is_multisite() ) {
			$table  = $wpdb->sitemeta;
			$column = 'meta_key';
		}

		$key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE {$column} LIKE %s", $key ) ); // @codingStandardsIgnoreLine.

		return $this;
	}

	/**
	 * Kill process.
	 *
	 * Stop processing queue items, clear cronjob and delete all batches.
	 *
	 * @return void
	 */
	public function kill_process() {
		if ( ! $this->is_queue_empty() ) {
			$this->delete_all_batches();
			wp_clear_scheduled_hook( $this->cron_hook_identifier );
		}
	}
}
