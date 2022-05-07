<?php

namespace AweBooking\System;

use AweBooking\PMS\Contracts\InitializableInterface;
use AweBooking\PMS\Contracts\RegistrableInterface;
use AweBooking\System\Bridge\SymfonyEventDispatcher;
use AweBooking\System\Database\ConnectionResolver;
use AweBooking\System\Database\Migration\DatabaseMigrationRepository;
use AweBooking\System\Database\Migration\Migrator;
use AweBooking\System\Database\Model;
use AweBooking\Vendor\Illuminate\Container\Container as BaseContainer;
use AweBooking\Vendor\Illuminate\Contracts\Events\Dispatcher;
use AweBooking\Vendor\Illuminate\Pagination\AbstractPaginator;
use AweBooking\Vendor\Illuminate\Support\HtmlString;

use function AweBooking\Vendor\trait_uses_recursive;

class Container extends BaseContainer
{
    /**
     * @var static
     */
    protected static $instance;

    /**
     * @var bool
     */
    protected static $isBooted = false;

    /**
     * Container constructor.
     */
    public function __construct()
    {
        static::$instance = $this;

        $this->instance(self::class, $this);

        if (!static::$isBooted) {
            $this->boot();

            static::$isBooted = true;
        }
    }

    /**
     * Bootstrap.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerBindings();
        $this->registerPaginationState();

        Model::setEventDispatcher($this->make(Dispatcher::class));
        Model::setConnectionResolver(new ConnectionResolver());

        $this->resolving(function ($resolved) {
            // Auto-inject container when ContainerAwareTrait used in the instance.
            if (in_array(ContainerAwareTrait::class, trait_uses_recursive($resolved), true)) {
                $resolved->setContainer($this);
            }

            if ($resolved instanceof RegistrableInterface) {
                $resolved->register($this);
            }

            if ($resolved instanceof InitializableInterface) {
                $resolved->initialize($this);
            }
        });
    }

    /**
     * Register core bindings.
     */
    protected function registerBindings(): void
    {
        $this->singleton(Dispatcher::class, function () {
            return new EventDispatcher($this);
        });

        $this->singleton(SymfonyEventDispatcher::class, function () {
            return new SymfonyEventDispatcher($this->get(Dispatcher::class));
        });

        $this->alias(Dispatcher::class, EventDispatcher::class);

        $this->bind(Migrator::class, function ($config) {
            $migrator = new Migrator(
                new DatabaseMigrationRepository($config['migrator_table_name'])
            );

            $migrator->path($config['migrations_path']);

            return $migrator;
        });
    }

    /**
     * Bind the pagination state resolvers.
     */
    protected function registerPaginationState()
    {
        AbstractPaginator::currentPageResolver(function ($pageName) {
            return absint($_REQUEST[$pageName] ?? 1);
        });

        AbstractPaginator::currentPathResolver(function () {
            return wp_parse_url(
                esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'] ?? '/')),
                PHP_URL_PATH
            );
        });

        AbstractPaginator::queryStringResolver(function () {
            return wp_unslash($_GET);
        });

        AbstractPaginator::viewFactoryResolver(function () {
            return new class {
                public function make($view, $args = [])
                {
                    extract($args, EXTR_SKIP);

                    ob_start();
                    if (strpos('simple', $view) === 0) {
                        include __DIR__ . '/../../resources/templates/pagination/simple.php';
                    } else {
                        include __DIR__ . '/../../resources/templates/pagination/default.php';
                    }

                    return new HtmlString(ob_get_clean());
                }
            };
        });
    }
}
