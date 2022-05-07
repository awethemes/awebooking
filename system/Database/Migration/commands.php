<?php

namespace AweBooking\System\Database\Migration;

use AweBooking\System\Container;
use Throwable;
use WP_CLI;

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

/**
 * Run the database migrations.
 *
 * ## OPTIONS
 *
 * [--yes]
 * : Force the operation to run when in production.
 *
 * [--step]
 * : Force the migrations to be run, so they can be rolled back individually.
 *
 * @when after_wp_load
 */
WP_CLI::add_command('pod migrate', function ($args, $options) {
    if (!defined('WP_DEBUG') || (defined('WP_DEBUG') && false === WP_DEBUG)) {
        WP_CLI::confirm('Are you sure you want to migrate the database?', $options);
    }

    try {
        /** @var Migrator $migrator */
        $migrator = Container::getInstance()->get(Migrator::class);
    } catch (Throwable $e) {
        WP_CLI::error($e);

        exit(1);
    }

    if (!$migrator->repositoryExists()) {
        $migrator->getRepository()->createRepository();

        WP_CLI::success('Migration table created successfully.');
    }

    $migrator->run(
        $migrator->paths(),
        $options
    );
});

/**
 * Rollback the last database migration
 *
 * ## OPTIONS
 *
 * [--yes]
 * : Force the operation to run when in production.
 *
 * [--step]
 * : Force the migrations to be run, so they can be rolled back individually.
 *
 * @when after_wp_load
 */
WP_CLI::add_command('pod migrate:rollback', function ($args, $options) {
    if (!defined('WP_DEBUG') || (defined('WP_DEBUG') && false === WP_DEBUG)) {
        WP_CLI::confirm('Are you sure you want to rollback the database?', $options);
    }

    try {
        /** @var Migrator $migrator */
        $migrator = Container::getInstance()->get(Migrator::class);
    } catch (Throwable $e) {
        WP_CLI::error($e);

        exit(1);
    }

    $migrator->rollback($migrator->paths(), $options);
});
