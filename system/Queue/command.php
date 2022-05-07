<?php

namespace AweBooking\System\Queue;

use Resque_Log as Logger;
use Resque_Worker as Worker;
use Throwable;
use WP_CLI;

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

/**
 * Run the queue worker.
 *
 * ## OPTIONS
 *
 * [--verbose]
 * : Show more output.
 *
 * [--queue=<queue>]
 * : The queue to process.
 *
 * @when after_wp_load
 */
WP_CLI::add_command('pod queue:work', function ($args, $options) {
    $queue = $options['queue'] ?? 'default';

    $worker = new Worker(wp_parse_list($queue));
    $worker->setLogger(new Logger($options['verbose'] ?? false));
    $worker->hasParent = false;

    WP_CLI::line('Starting worker: ' . $worker);

    try {
        $worker->work(5, false);
    } catch (Throwable $e) {
        WP_CLI::error($e->getMessage());
    }
});
