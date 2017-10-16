<?php
namespace AweBooking\Providers;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Handler\GroupHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use AweBooking\Support\Service_Hooks as Service_Provider;

class Monolog_Service_Provider extends Service_Provider {
	/**
	 * Registers services on the given container.
	 *
	 * @param AweBooking $awebooking AweBooking Container instance.
	 */
	public function register( $awebooking ) {
		$awebooking['monolog.logfile'] = trailingslashit( WP_CONTENT_DIR ) . 'awebooking.log';
		$awebooking['monolog.level']   = Logger::DEBUG;

		$awebooking->bind( 'monolog.handler', function () use ( $awebooking ) {
			$handler = new StreamHandler( $awebooking['monolog.logfile'], $awebooking['monolog.level'], true, 0644 );
			$handler->setFormatter( new LineFormatter );

			return $handler;
		});

		$awebooking->bind( 'monolog.handlers', function () use ( $awebooking ) {
			$handlers = apply_filters( 'awebooking/logger_handlers', [] );

			if ( $awebooking['monolog.logfile'] ) {
				$handlers[] = $awebooking['monolog.handler'];
			}

			return $handlers;
		});

		$awebooking->singleton( 'monolog', function ( $awebooking ) {
			$log = new Logger( 'awebooking' );

			$handler = new GroupHandler( $awebooking['monolog.handlers'] );
			$log->pushHandler( $handler );

			return $log;
		});

		$awebooking->alias( 'monolog', 'logger' );
		$awebooking->alias( 'monolog', Logger::class );
		$awebooking->alias( 'monolog', LoggerInterface::class );
	}
}
