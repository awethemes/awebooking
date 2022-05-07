<?php

namespace AweBooking\System\Database\Migration;

use AweBooking\System\Database\Connection;
use AweBooking\Vendor\Illuminate\Contracts\Events\Dispatcher;
use AweBooking\Vendor\Illuminate\Database\Migrations\MigrationRepositoryInterface;
use AweBooking\Vendor\Illuminate\Database\Migrations\Migrator as IlluminateMigrator;
use AweBooking\Vendor\Illuminate\Support\Collection;
use AweBooking\Vendor\Illuminate\Support\Str;
use WP_CLI;

class Migrator extends IlluminateMigrator
{
    /**
     * @var array
     */
    protected $migrations = [];

    /**
     * @param MigrationRepositoryInterface $repository
     * @param Dispatcher|null $dispatcher
     */
    public function __construct(MigrationRepositoryInterface $repository, Dispatcher $dispatcher = null)
    {
        $this->repository = $repository;
        $this->events = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getMigrationFiles($paths)
    {
        return Collection::make($paths)->flatMap(function ($path) {
            return Str::endsWith($path, '.php') ? [$path] : glob($path . '/*_*.php');
        })->filter()->values()->keyBy(function ($file) {
            return $this->getMigrationName($file);
        })->sortBy(function ($file, $key) {
            return $key;
        })->all();
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($name)
    {
        return $this->migrations[$name];
    }

    /**
     * {@inheritdoc}
     */
    protected function resolvePath(string $path)
    {
        return $this->migrations[$this->getMigrationName($path)];
    }

    /**
     * {@inheritdoc}
     */
    public function requireFiles(array $files)
    {
        foreach ($files as $file) {
            $this->migrations[$this->getMigrationName($file)] = require $file;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function resolveConnection($connection)
    {
        return Connection::getInstance();
    }

    /**
     * {@inheritdoc}
     */
    protected function note($message)
    {
        if (!defined('WP_CLI') || !WP_CLI) {
            return;
        }

        if (0 === strpos($message, '<fg=red>')) {
            WP_CLI::error(strip_tags($message));

            return;
        }

        $replacements = [
            '<info>' => '%B',
            '</info>' => '%n',
            '<comment>' => '%Y',
            '</comment>' => '%n',
        ];

        $message = WP_CLI::colorize(str_replace(array_keys($replacements), array_values($replacements), $message));
        WP_CLI::log($message);
    }
}
