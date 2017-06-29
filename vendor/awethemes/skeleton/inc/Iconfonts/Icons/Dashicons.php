<?php
namespace Skeleton\Iconfonts\Icons;

class Dashicons extends Iconpack {
	/**
	 * Iconpack unique ID.
	 *
	 * @var string
	 */
	public $id = 'dashicons';

	/**
	 * Iconpack display name.
	 *
	 * @var string
	 */
	public $name = 'Dashicons';

	/**
	 * Iconpack version.
	 *
	 * @var string
	 */
	public $version = '4.3.1';

	/**
	 * Stylesheet ID.
	 *
	 * @var string
	 */
	public $stylesheet_id = 'dashicons';

	/**
	 * Return an array icon groups.
	 *
	 * @return array
	 */
	public function groups() {
		$groups = array(
			array(
				'id'   => 'admin',
				'name' => esc_html__( 'Admin', 'skeleton' ),
			),
			array(
				'id'   => 'post-formats',
				'name' => esc_html__( 'Post Formats', 'skeleton' ),
			),
			array(
				'id'   => 'welcome-screen',
				'name' => esc_html__( 'Welcome Screen', 'skeleton' ),
			),
			array(
				'id'   => 'image-editor',
				'name' => esc_html__( 'Image Editor', 'skeleton' ),
			),
			array(
				'id'   => 'text-editor',
				'name' => esc_html__( 'Text Editor', 'skeleton' ),
			),
			array(
				'id'   => 'post',
				'name' => esc_html__( 'Post', 'skeleton' ),
			),
			array(
				'id'   => 'sorting',
				'name' => esc_html__( 'Sorting', 'skeleton' ),
			),
			array(
				'id'   => 'social',
				'name' => esc_html__( 'Social', 'skeleton' ),
			),
			array(
				'id'   => 'jobs',
				'name' => esc_html__( 'Jobs', 'skeleton' ),
			),
			array(
				'id'   => 'products',
				'name' => esc_html__( 'Internal/Products', 'skeleton' ),
			),
			array(
				'id'   => 'taxonomies',
				'name' => esc_html__( 'Taxonomies', 'skeleton' ),
			),
			array(
				'id'   => 'alerts',
				'name' => esc_html__( 'Alerts/Notifications', 'skeleton' ),
			),
			array(
				'id'   => 'media',
				'name' => esc_html__( 'Media', 'skeleton' ),
			),
			array(
				'id'   => 'misc',
				'name' => esc_html__( 'Misc./Post Types', 'skeleton' ),
			),
		);

		/**
		 * Filter dashicon groups
		 *
		 * @param array $groups Icon groups.
		 */
		$groups = apply_filters( 'skeleton/iconfonts/dashicons/group', $groups );

		return $groups;
	}

	/**
	 * Return an array of icons.
	 *
	 * @return array
	 */
	public function icons() {
		$icons = array(
			array(
				'id'    => 'dashicons-admin-appearance',
				'name'  => esc_html__( 'Appearance', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-collapse',
				'name'  => esc_html__( 'Collapse', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-comments',
				'name'  => esc_html__( 'Comments', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-customizer',
				'name'  => esc_html__( 'Customizer', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-dashboard',
				'name'  => esc_html__( 'Dashboard', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-generic',
				'name'  => esc_html__( 'Generic', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-filter',
				'name'  => esc_html__( 'Filter', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-home',
				'name'  => esc_html__( 'Home', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-media',
				'name'  => esc_html__( 'Media', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-menu',
				'name'  => esc_html__( 'Menu', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-multisite',
				'name'  => esc_html__( 'Multisite', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-network',
				'name'  => esc_html__( 'Network', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-page',
				'name'  => esc_html__( 'Page', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-plugins',
				'name'  => esc_html__( 'Plugins', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-settings',
				'name'  => esc_html__( 'Settings', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-site',
				'name'  => esc_html__( 'Site', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-tools',
				'name'  => esc_html__( 'Tools', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-users',
				'name'  => esc_html__( 'Users', 'skeleton' ),
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-format-standard',
				'name'  => esc_html__( 'Standard', 'skeleton' ),
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-aside',
				'name'  => esc_html__( 'Aside', 'skeleton' ),
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-image',
				'name'  => esc_html__( 'Image', 'skeleton' ),
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-video',
				'name'  => esc_html__( 'Video', 'skeleton' ),
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-audio',
				'name'  => esc_html__( 'Audio', 'skeleton' ),
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-quote',
				'name'  => esc_html__( 'Quote', 'skeleton' ),
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-gallery',
				'name'  => esc_html__( 'Gallery', 'skeleton' ),
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-links',
				'name'  => esc_html__( 'Links', 'skeleton' ),
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-status',
				'name'  => esc_html__( 'Status', 'skeleton' ),
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-chat',
				'name'  => esc_html__( 'Chat', 'skeleton' ),
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-welcome-add-page',
				'name'  => esc_html__( 'Add page', 'skeleton' ),
				'group' => 'welcome-screen',
			),
			array(
				'id'    => 'dashicons-welcome-comments',
				'name'  => esc_html__( 'Comments', 'skeleton' ),
				'group' => 'welcome-screen',
			),
			array(
				'id'    => 'dashicons-welcome-edit-page',
				'name'  => esc_html__( 'Edit page', 'skeleton' ),
				'group' => 'welcome-screen',
			),
			array(
				'id'    => 'dashicons-welcome-learn-more',
				'name'  => esc_html__( 'Learn More', 'skeleton' ),
				'group' => 'welcome-screen',
			),
			array(
				'id'    => 'dashicons-welcome-view-site',
				'name'  => esc_html__( 'View Site', 'skeleton' ),
				'group' => 'welcome-screen',
			),
			array(
				'id'    => 'dashicons-welcome-widgets-menus',
				'name'  => esc_html__( 'Widgets', 'skeleton' ),
				'group' => 'welcome-screen',
			),
			array(
				'id'    => 'dashicons-welcome-write-blog',
				'name'  => esc_html__( 'Write Blog', 'skeleton' ),
				'group' => 'welcome-screen',
			),
			array(
				'id'    => 'dashicons-image-crop',
				'name'  => esc_html__( 'Crop', 'skeleton' ),
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-image-filter',
				'name'  => esc_html__( 'Filter', 'skeleton' ),
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-image-rotate',
				'name'  => esc_html__( 'Rotate', 'skeleton' ),
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-image-rotate-left',
				'name'  => esc_html__( 'Rotate Left', 'skeleton' ),
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-image-rotate-right',
				'name'  => esc_html__( 'Rotate Right', 'skeleton' ),
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-image-flip-vertical',
				'name'  => esc_html__( 'Flip Vertical', 'skeleton' ),
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-image-flip-horizontal',
				'name'  => esc_html__( 'Flip Horizontal', 'skeleton' ),
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-undo',
				'name'  => esc_html__( 'Undo', 'skeleton' ),
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-redo',
				'name'  => esc_html__( 'Redo', 'skeleton' ),
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-editor-bold',
				'name'  => esc_html__( 'Bold', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-italic',
				'name'  => esc_html__( 'Italic', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-ul',
				'name'  => esc_html__( 'Unordered List', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-ol',
				'name'  => esc_html__( 'Ordered List', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-quote',
				'name'  => esc_html__( 'Quote', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-alignleft',
				'name'  => esc_html__( 'Align Left', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-aligncenter',
				'name'  => esc_html__( 'Align Center', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-alignright',
				'name'  => esc_html__( 'Align Right', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-insertmore',
				'name'  => esc_html__( 'Insert More', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-spellcheck',
				'name'  => esc_html__( 'Spell Check', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-distractionfree',
				'name'  => esc_html__( 'Distraction-free', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-kitchensink',
				'name'  => esc_html__( 'Kitchensink', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-underline',
				'name'  => esc_html__( 'Underline', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-justify',
				'name'  => esc_html__( 'Justify', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-textcolor',
				'name'  => esc_html__( 'Text Color', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-paste-word',
				'name'  => esc_html__( 'Paste Word', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-paste-text',
				'name'  => esc_html__( 'Paste Text', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-removeformatting',
				'name'  => esc_html__( 'Clear Formatting', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-video',
				'name'  => esc_html__( 'Video', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-customchar',
				'name'  => esc_html__( 'Custom Characters', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-indent',
				'name'  => esc_html__( 'Indent', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-outdent',
				'name'  => esc_html__( 'Outdent', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-help',
				'name'  => esc_html__( 'Help', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-strikethrough',
				'name'  => esc_html__( 'Strikethrough', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-unlink',
				'name'  => esc_html__( 'Unlink', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-rtl',
				'name'  => esc_html__( 'RTL', 'skeleton' ),
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-align-left',
				'name'  => esc_html__( 'Align Left', 'skeleton' ),
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-align-right',
				'name'  => esc_html__( 'Align Right', 'skeleton' ),
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-align-center',
				'name'  => esc_html__( 'Align Center', 'skeleton' ),
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-align-none',
				'name'  => esc_html__( 'Align None', 'skeleton' ),
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-lock',
				'name'  => esc_html__( 'Lock', 'skeleton' ),
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-calendar',
				'name'  => esc_html__( 'Calendar', 'skeleton' ),
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-calendar-alt',
				'name'  => esc_html__( 'Calendar', 'skeleton' ),
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-hidden',
				'name'  => esc_html__( 'Hidden', 'skeleton' ),
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-visibility',
				'name'  => esc_html__( 'Visibility', 'skeleton' ),
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-post-status',
				'name'  => esc_html__( 'Post Status', 'skeleton' ),
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-post-trash',
				'name'  => esc_html__( 'Post Trash', 'skeleton' ),
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-edit',
				'name'  => esc_html__( 'Edit', 'skeleton' ),
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-trash',
				'name'  => esc_html__( 'Trash', 'skeleton' ),
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-arrow-up',
				'name'  => esc_html__( 'Arrow: Up', 'skeleton' ),
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-down',
				'name'  => esc_html__( 'Arrow: Down', 'skeleton' ),
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-left',
				'name'  => esc_html__( 'Arrow: Left', 'skeleton' ),
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-right',
				'name'  => esc_html__( 'Arrow: Right', 'skeleton' ),
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-up-alt',
				'name'  => esc_html__( 'Arrow: Up', 'skeleton' ),
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-down-alt',
				'name'  => esc_html__( 'Arrow: Down', 'skeleton' ),
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-left-alt',
				'name'  => esc_html__( 'Arrow: Left', 'skeleton' ),
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-right-alt',
				'name'  => esc_html__( 'Arrow: Right', 'skeleton' ),
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-up-alt2',
				'name'  => esc_html__( 'Arrow: Up', 'skeleton' ),
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-down-alt2',
				'name'  => esc_html__( 'Arrow: Down', 'skeleton' ),
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-left-alt2',
				'name'  => esc_html__( 'Arrow: Left', 'skeleton' ),
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-right-alt2',
				'name'  => esc_html__( 'Arrow: Right', 'skeleton' ),
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-leftright',
				'name'  => esc_html__( 'Left-Right', 'skeleton' ),
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-sort',
				'name'  => esc_html__( 'Sort', 'skeleton' ),
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-list-view',
				'name'  => esc_html__( 'List View', 'skeleton' ),
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-exerpt-view',
				'name'  => esc_html__( 'Excerpt View', 'skeleton' ),
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-grid-view',
				'name'  => esc_html__( 'Grid View', 'skeleton' ),
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-share',
				'name'  => esc_html__( 'Share', 'skeleton' ),
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-share-alt',
				'name'  => esc_html__( 'Share', 'skeleton' ),
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-share-alt2',
				'name'  => esc_html__( 'Share', 'skeleton' ),
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-twitter',
				'name'  => esc_html__( 'Twitter', 'skeleton' ),
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-rss',
				'name'  => esc_html__( 'RSS', 'skeleton' ),
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-email',
				'name'  => esc_html__( 'Email', 'skeleton' ),
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-email-alt',
				'name'  => esc_html__( 'Email', 'skeleton' ),
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-facebook',
				'name'  => esc_html__( 'Facebook', 'skeleton' ),
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-facebook-alt',
				'name'  => esc_html__( 'Facebook', 'skeleton' ),
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-googleplus',
				'name'  => esc_html__( 'Google+', 'skeleton' ),
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-networking',
				'name'  => esc_html__( 'Networking', 'skeleton' ),
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-art',
				'name'  => esc_html__( 'Art', 'skeleton' ),
				'group' => 'jobs',
			),
			array(
				'id'    => 'dashicons-hammer',
				'name'  => esc_html__( 'Hammer', 'skeleton' ),
				'group' => 'jobs',
			),
			array(
				'id'    => 'dashicons-migrate',
				'name'  => esc_html__( 'Migrate', 'skeleton' ),
				'group' => 'jobs',
			),
			array(
				'id'    => 'dashicons-performance',
				'name'  => esc_html__( 'Performance', 'skeleton' ),
				'group' => 'jobs',
			),
			array(
				'id'    => 'dashicons-wordpress',
				'name'  => esc_html__( 'WordPress', 'skeleton' ),
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-wordpress-alt',
				'name'  => esc_html__( 'WordPress', 'skeleton' ),
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-pressthis',
				'name'  => esc_html__( 'PressThis', 'skeleton' ),
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-update',
				'name'  => esc_html__( 'Update', 'skeleton' ),
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-screenoptions',
				'name'  => esc_html__( 'Screen Options', 'skeleton' ),
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-info',
				'name'  => esc_html__( 'Info', 'skeleton' ),
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-cart',
				'name'  => esc_html__( 'Cart', 'skeleton' ),
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-feedback',
				'name'  => esc_html__( 'Feedback', 'skeleton' ),
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-cloud',
				'name'  => esc_html__( 'Cloud', 'skeleton' ),
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-translation',
				'name'  => esc_html__( 'Translation', 'skeleton' ),
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-tag',
				'name'  => esc_html__( 'Tag', 'skeleton' ),
				'group' => 'taxonomies',
			),
			array(
				'id'    => 'dashicons-category',
				'name'  => esc_html__( 'Category', 'skeleton' ),
				'group' => 'taxonomies',
			),
			array(
				'id'    => 'dashicons-yes',
				'name'  => esc_html__( 'Yes', 'skeleton' ),
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-no',
				'name'  => esc_html__( 'No', 'skeleton' ),
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-no-alt',
				'name'  => esc_html__( 'No', 'skeleton' ),
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-plus',
				'name'  => esc_html__( 'Plus', 'skeleton' ),
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-minus',
				'name'  => esc_html__( 'Minus', 'skeleton' ),
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-dismiss',
				'name'  => esc_html__( 'Dismiss', 'skeleton' ),
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-marker',
				'name'  => esc_html__( 'Marker', 'skeleton' ),
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-star-filled',
				'name'  => esc_html__( 'Star: Filled', 'skeleton' ),
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-star-half',
				'name'  => esc_html__( 'Star: Half', 'skeleton' ),
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-star-empty',
				'name'  => esc_html__( 'Star: Empty', 'skeleton' ),
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-flag',
				'name'  => esc_html__( 'Flag', 'skeleton' ),
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-controls-skipback',
				'name'  => esc_html__( 'Skip Back', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-controls-back',
				'name'  => esc_html__( 'Back', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-controls-play',
				'name'  => esc_html__( 'Play', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-controls-pause',
				'name'  => esc_html__( 'Pause', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-controls-forward',
				'name'  => esc_html__( 'Forward', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-controls-skipforward',
				'name'  => esc_html__( 'Skip Forward', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-controls-repeat',
				'name'  => esc_html__( 'Repeat', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-controls-volumeon',
				'name'  => esc_html__( 'Volume: On', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-controls-volumeoff',
				'name'  => esc_html__( 'Volume: Off', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-archive',
				'name'  => esc_html__( 'Archive', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-audio',
				'name'  => esc_html__( 'Audio', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-code',
				'name'  => esc_html__( 'Code', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-default',
				'name'  => esc_html__( 'Default', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-document',
				'name'  => esc_html__( 'Document', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-interactive',
				'name'  => esc_html__( 'Interactive', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-spreadsheet',
				'name'  => esc_html__( 'Spreadsheet', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-text',
				'name'  => esc_html__( 'Text', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-video',
				'name'  => esc_html__( 'Video', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-playlist-audio',
				'name'  => esc_html__( 'Audio Playlist', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-playlist-video',
				'name'  => esc_html__( 'Video Playlist', 'skeleton' ),
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-album',
				'name'  => esc_html__( 'Album', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-analytics',
				'name'  => esc_html__( 'Analytics', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-awards',
				'name'  => esc_html__( 'Awards', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-backup',
				'name'  => esc_html__( 'Backup', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-building',
				'name'  => esc_html__( 'Building', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-businessman',
				'name'  => esc_html__( 'Businessman', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-camera',
				'name'  => esc_html__( 'Camera', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-carrot',
				'name'  => esc_html__( 'Carrot', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-chart-pie',
				'name'  => esc_html__( 'Chart: Pie', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-chart-bar',
				'name'  => esc_html__( 'Chart: Bar', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-chart-line',
				'name'  => esc_html__( 'Chart: Line', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-chart-area',
				'name'  => esc_html__( 'Chart: Area', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-desktop',
				'name'  => esc_html__( 'Desktop', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-forms',
				'name'  => esc_html__( 'Forms', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-groups',
				'name'  => esc_html__( 'Groups', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-id',
				'name'  => esc_html__( 'ID', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-id-alt',
				'name'  => esc_html__( 'ID', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-images-alt',
				'name'  => esc_html__( 'Images', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-images-alt2',
				'name'  => esc_html__( 'Images', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-index-card',
				'name'  => esc_html__( 'Index Card', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-layout',
				'name'  => esc_html__( 'Layout', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-location',
				'name'  => esc_html__( 'Location', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-location-alt',
				'name'  => esc_html__( 'Location', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-products',
				'name'  => esc_html__( 'Products', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-portfolio',
				'name'  => esc_html__( 'Portfolio', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-book',
				'name'  => esc_html__( 'Book', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-book-alt',
				'name'  => esc_html__( 'Book', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-download',
				'name'  => esc_html__( 'Download', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-upload',
				'name'  => esc_html__( 'Upload', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-clock',
				'name'  => esc_html__( 'Clock', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-lightbulb',
				'name'  => esc_html__( 'Lightbulb', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-money',
				'name'  => esc_html__( 'Money', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-palmtree',
				'name'  => esc_html__( 'Palm Tree', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-phone',
				'name'  => esc_html__( 'Phone', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-search',
				'name'  => esc_html__( 'Search', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-shield',
				'name'  => esc_html__( 'Shield', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-shield-alt',
				'name'  => esc_html__( 'Shield', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-slides',
				'name'  => esc_html__( 'Slides', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-smartphone',
				'name'  => esc_html__( 'Smartphone', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-smiley',
				'name'  => esc_html__( 'Smiley', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-sos',
				'name'  => esc_html__( 'S.O.S.', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-sticky',
				'name'  => esc_html__( 'Sticky', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-store',
				'name'  => esc_html__( 'Store', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-tablet',
				'name'  => esc_html__( 'Tablet', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-testimonial',
				'name'  => esc_html__( 'Testimonial', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-tickets-alt',
				'name'  => esc_html__( 'Tickets', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-thumbs-up',
				'name'  => esc_html__( 'Thumbs Up', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-thumbs-down',
				'name'  => esc_html__( 'Thumbs Down', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-unlock',
				'name'  => esc_html__( 'Unlock', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-vault',
				'name'  => esc_html__( 'Vault', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-video-alt',
				'name'  => esc_html__( 'Video', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-video-alt2',
				'name'  => esc_html__( 'Video', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-video-alt3',
				'name'  => esc_html__( 'Video', 'skeleton' ),
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-warning',
				'name'  => esc_html__( 'Warning', 'skeleton' ),
				'group' => 'misc',
			),
		);

		/**
		 * Filter dashicon icons.
		 *
		 * @param array $icons Icon names.
		 */
		$icons = apply_filters( 'skeleton/iconfonts/dashicons/icons', $icons );

		return $icons;
	}
}
