<?php

namespace AweBooking\PMS\Features\GutenbergEverywhere\Handlers;

use AweBooking\Features\GutenbergEverywhere\GutenbergEditor;
use AweBooking\Traits\AssetsTrait;

abstract class GutenbergHandler
{
    use AssetsTrait;

    /**
     * @var GutenbergEditor
     */
    protected $gutenberg;

    /**
     * @var string
     */
    private $doing_hook;

    /**
     * @param string $currentHook
     * @param \WP_Screen $screen
     * @return false
     */
    public function canShowAdminEditor($currentHook, $screen)
    {
        return false;
    }

    /**
     * Load Gutenberg if a comment form is enabled
     *
     * @return void
     */
    public function loadEditor()
    {
        $this->gutenberg = new GutenbergEditor();
        $this->gutenberg->load();
    }

    /**
     * Register/enqueue admin scripts.
     *
     * @return void
     */
    public function enqueueAdminScripts()
    {
    }

    /**
     * @return array
     */
    public function getEditorSettings()
    {
        if (!$this->gutenberg) {
            $this->loadEditor();
        }

        // Settings for the editor
        return [
            'editorType' => $this->get_editor_type(),
            'editor' => $this->gutenberg->get_editor_settings(),
            'iso' => [
                'allowApi' => true,
                'blocks' => [
                    'allowBlocks' => $this->get_allowed_blocks(),
                ],
                'toolbar' => [
                    'inserter' => true,
                    'inspector' => true,
                    'navigation' => true,
                ],
                'sidebar' => [
                    'inserter' => true,
                    'inspector' => true,
                ],
                'moreMenu' => [
                    'topToolbar' => true,
                ],
            ],
        ];
    }

    /**
     * Direct copy of core `do_blocks`, but for comments.
     *
     * This also has the benefit that we don't run `wpautop` on block transformed comments,
     * potentially breaking them.
     *
     * @param String $content Comment text
     * @return String
     */
    public function do_blocks($content, $hook)
    {
        $blocks = parse_blocks($content);
        $output = '';

        foreach ($blocks as $block) {
            $output .= render_block($block);
        }

        // If there are blocks in this content, we shouldn't run wpautop() on it later.
        $priority = has_filter($hook, 'wpautop');

        if (false !== $priority && doing_filter($hook) && has_blocks($content)) {
            $this->doing_hook = $hook;
            remove_filter($hook, 'wpautop', $priority);
            add_filter($hook, [$this, 'restore_wpautop_hook'], $priority + 1);
        }

        return ltrim($output);
    }

    /**
     * Restore the above `remove_filter` for comments
     *
     * @param String $content Comment ext
     * @return String
     **/
    public function restore_wpautop_hook($content)
    {
        $current_priority = has_filter($this->doing_hook, [$this, 'restore_wpautop_hook']);

        if ($current_priority !== false) {
            add_filter($this->doing_hook, 'wpautop', $current_priority - 1);
            remove_filter($this->doing_hook, [$this, 'restore_wpautop_hook'], $current_priority);
        }

        $this->doing_hook = null;

        return $content;
    }

    public function wp_editor_settings($settings, $editor_id)
    {
        $settings['tinymce'] = false;
        $settings['quicktags'] = false;

        return $settings;
    }

    public function get_editor_type()
    {
        return '';
    }

    /**
     * Remove blocks that aren't allowed
     *
     * @param string $content
     * @return string
     */
    public function remove_blocks($content)
    {
        if (!has_blocks($content)) {
            return $content;
        }

        $allowed = $this->get_allowed_blocks();
        $blocks = parse_blocks($content);
        $output = '';

        foreach ($blocks as $block) {
            if (in_array($block['blockName'], $allowed, true)) {
                $output .= serialize_block($block);
            }
        }

        return ltrim($output);
    }

    /**
     * Get a list of allowed blocks by looking at the allowed comment tags
     *
     * @return string[]
     */
    private function get_allowed_blocks()
    {
        global $allowedtags;

        $allowed = [
            'core/paragraph',
            'core/heading',
            'core/gallery',
            'core/image',
            'core/list',
            'core/button',
            'core/buttons',
            'core/code',
            'core/shortcode',
            'core/html',
            'core/media-text',
            'core/columns',
            'core/column',
            'core/cover',
            'core/embed',
            'core/group',
            'core/spacer',
            'core/text-columns',
            'core/video',
        ];

        $convert = [
            'blockquote' => 'core/quote',
            'h1' => 'core/heading',
            'h2' => 'core/heading',
            'h3' => 'core/heading',
            'img' => 'core/image',
            'ul' => 'core/list',
            'ol' => 'core/list',
            'pre' => 'core/code',
            'table' => 'core/table',
            'video' => 'core/video',
        ];

        foreach (array_keys($allowedtags) as $tag) {
            if (isset($convert[$tag])) {
                $allowed[] = $convert[$tag];
            }
        }

        return apply_filters(
            'gutenberg_everywhere_allowed_blocks',
            array_unique($allowed),
            $this->get_editor_type()
        );
    }
}
