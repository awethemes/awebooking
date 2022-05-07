<?php

namespace AweBooking\PMS\Features\GutenbergEverywhere\Handlers;

use WP_Screen;

class MegaMenuHandler extends GutenbergHandler
{
    public function __construct()
    {
        add_action('wp_nav_menu_item_custom_fields', function ($item_id, $menu_item) {
            echo sprintf(
                '<p class="menu-item-megamenu"><button class="button edit-megamenu" type="button" data-menu-id="%d">Mega Menu</button></p>',
                $item_id
            );
        }, 10, 2);
    }

    public function loadEditor()
    {
        parent::loadEditor();

        wp_enqueue_style(
            'pod-edit-megamenu',
            $this->getAssetUrl('edit-megamenu.css'),
            [],
            $this->getAssetInfo('edit-megamenu', 'version')
        );

        wp_enqueue_script(
            'pod-edit-megamenu',
            $this->getAssetUrl('edit-megamenu.js'),
            $this->getAssetInfo('edit-megamenu', 'dependencies'),
            $this->getAssetInfo('edit-megamenu', 'version'),
            true
        );

        wp_localize_script(
            'pod-edit-megamenu',
            '_podGutenbergMegaMenu',
            $this->getEditorSettings()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function canShowAdminEditor($currentHook, $screen)
    {
        return $currentHook === 'nav-menus.php';
    }
}
