<?php

namespace AweBooking\PMS\Dashboard;

use AweBooking\PMS\Traits\AssetsTrait;
use AweBooking\System\Container;
use AweBooking\System\Inertia\InertiaInterface;
use AweBooking\System\Inertia\Response;
use AweBooking\Vendor\Symfony\Component\HttpFoundation\Request;
use Throwable;

abstract class InertiaPage
{
    use AssetsTrait;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $menuTitle;

    /**
     * @var string
     */
    public $capability = 'manage_awebooking';

    /**
     * @var InertiaInterface
     */
    protected $inertia;

    /**
     * Response the view.
     *
     * @return Response|void
     */
    abstract public function view(): ?Response;

    /**
     * Invoke the screen.
     *
     * @return void
     */
    public function __invoke()
    {
        if (!$this->inertia) {
            $this->setupInertia();
        }

        try {
            if ($response = $this->view()) {
                $response->output();
            }
        } catch (Throwable $e) {
            $this->reportException($e);

            $this->renderException($e);
        }
    }

    /**
     * Set the page.
     *
     * @return void
     */
    public function setup(): void
    {
        $request = Request::createFromGlobals();

        $_GET['noheader'] = true;

        if (!empty($_REQUEST['action'])) {
        }

        $this->setupInertia();
    }

    /**
     * @param string $action
     * @param Request $request
     * @return mixed|void
     */
    public function handle(string $action, Request $request)
    {
    }

    /**
     * Setup the Inertia.
     *
     * @return void
     */
    protected function setupInertia(): void
    {
        $this->inertia = Container::getInstance()->get(InertiaInterface::class);

        $this->inertia->setViewCallback(function (array $data) {
            $this->enqueueScripts();

            // Include the admin-header.
            require_once ABSPATH . 'wp-admin/admin-header.php';

            echo sprintf(
                '<div id="awebooking-root" class="awebooking wrap" data-page=\'%s\'></div>',
                _wp_specialchars(json_encode($data['page'] ?? []), ENT_QUOTES, 'UTF-8', true)
            );
        });
    }

    public function reportException(Throwable $e)
    {
        error_log($e, E_USER_ERROR);
    }

    public function renderException(Throwable $e)
    {
        throw $e;
    }

    private function enqueueScripts(): void
    {
        $this->registerAssets('dashboard');

        wp_enqueue_style('wp-components');
        wp_enqueue_style('awebooking-pms-dashboard');
        wp_enqueue_script('awebooking-pms-dashboard');
    }
}
