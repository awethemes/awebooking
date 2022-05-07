<?php

namespace AweBooking\PMS\Features\GutenbergEverywhere;

use AweBooking\Features\GutenbergEverywhere\Handlers\GutenbergHandler;
use AweBooking\Features\GutenbergEverywhere\Handlers\MegaMenuHandler;
use AweBooking\Traits\SingletonTrait;

class GutenbergEverywhere
{
    use SingletonTrait;

    /**
     * Gutenberg handlers
     *
     * @var GutenbergHandler[]
     */
    private $handlers = [];

    /**
     * Initialize the GutenbergEverywhere feature.
     *
     * @return void
     */
    public function init()
    {
        $this->registerHandler(new MegaMenuHandler());

        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
    }

    /**
     * Register the handler.
     *
     * @param GutenbergHandler $handler
     * @return $this
     */
    public function registerHandler(GutenbergHandler $handler)
    {
        $this->handlers[] = $handler;

        return $this;
    }

    /* Private methods */

    /**
     * Perform additional admin tasks.
     *
     * @param string $hook
     * @return void
     */
    public function admin_enqueue_scripts($hook)
    {
        $screen = get_current_screen();

        foreach ($this->handlers as $handler) {
            if ($handler->canShowAdminEditor($hook, $screen)) {
                $screen->is_block_editor(false);

                $handler->enqueueAdminScripts();

                // Setup the editor.
                add_action('admin_head', function () use ($handler) {
                    // add_filter('the_editor', [$handler, 'the_editor']);

                    add_filter('wp_editor_settings', [$handler, 'wp_editor_settings'], 10, 2);
                });

                // Stops a problem with the Gutenberg plugin accessing widgets that don't exist
                remove_action('admin_footer', 'gutenberg_block_editor_admin_footer');

                // Load Gutenberg in in_admin_header so WP admin doesn't set the 'block-editor-page' body class
                add_action('in_admin_header', function () use ($handler) {
                    $handler->loadEditor();
                });

                break;
            }
        }
    }
}
