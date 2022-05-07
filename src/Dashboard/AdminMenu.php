<?php

namespace AweBooking\PMS\Dashboard;

use AweBooking\PMS\Contracts\InitializableInterface;
use AweBooking\PMS\Contracts\RegistrableInterface;
use AweBooking\PMS\Traits\SingletonTrait;
use AweBooking\System\Container;
use AweBooking\System\Inertia\Inertia;
use AweBooking\System\Inertia\InertiaInterface;
use AweBooking\System\Inertia\InertiaRequest;
use Closure;

final class AdminMenu implements RegistrableInterface, InitializableInterface
{
    use SingletonTrait;

    /**
     * @var array
     */
    private $menuStack = [];

    /**
     * {@inheritdoc}
     */
    public function register(Container $container): void
    {
        $container->singleton(
            InertiaInterface::class,
            function () {
                return new Inertia(InertiaRequest::createFromGlobals(), '1.0');
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(Container $container): void
    {
        $inertia = $container->make(InertiaInterface::class);

        $inertia->share('user', function () {
            return wp_get_current_user();
        });

        $this
            ->registerPage('awebooking-pms', function (\stdClass $menuItem) {
                $menuItem->icon = 'dashicons-images-alt2';
                $menuItem->title = __('PMS', 'awebooking');
                $menuItem->menuTitle = __('PMS', 'awebooking');
            })
            ->registerSubmenuPage(Pages\DashboardView::class, 'awebooking-pms', 0);
    }

    /**
     * Register a menu page.
     *
     * @param string $name
     * @param Closure $setupCallback
     * @return $this
     */
    public function registerPage($name, Closure $setupCallback): self
    {
        $menuItem = new \stdClass();
        $menuItem->title = '';
        $menuItem->menuTitle = '';
        $menuItem->icon = '';
        $menuItem->position = 3;
        $menuItem->capability = 'manage_awebooking';

        $this->menuStack[] = $name;
        $setupCallback($menuItem);

        add_menu_page(
            $menuItem->title,
            $menuItem->menuTitle,
            $menuItem->capability,
            $name,
            null,
            $menuItem->icon,
            $menuItem->position
        );

        return $this;
    }

    /**
     * Add a sub-menu page.
     *
     * @param string $page
     * @param string $slug
     * @param int $position
     * @return $this
     */
    public function registerSubmenuPage(string $pageClass, string $slug, int $position = 50): self
    {
        $parentSlug = end($this->menuStack);
        if (false === $parentSlug) {
            return $this;
        }

        /** @var InertiaPage $page */
        $page = Container::getInstance()->make($pageClass);

        $hookName = add_submenu_page(
            $parentSlug,
            $page->title ?? $pageClass,
            $page->menuTitle ?? $pageClass,
            $page->capability ?? 'manage_awebooking',
            $slug,
            [$page, '__invoke'],
            $position
        );

        if ($hookName) {
            add_action('load-' . $hookName, [$page, 'setup']);
        }

        return $this;
    }
}
