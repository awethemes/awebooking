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
				'name' => 'Admin',
			),
			array(
				'id'   => 'post-formats',
				'name' => 'Post Formats',
			),
			array(
				'id'   => 'welcome-screen',
				'name' => 'Welcome Screen',
			),
			array(
				'id'   => 'image-editor',
				'name' => 'Image Editor',
			),
			array(
				'id'   => 'text-editor',
				'name' => 'Text Editor',
			),
			array(
				'id'   => 'post',
				'name' => 'Post',
			),
			array(
				'id'   => 'sorting',
				'name' => 'Sorting',
			),
			array(
				'id'   => 'social',
				'name' => 'Social',
			),
			array(
				'id'   => 'jobs',
				'name' => 'Jobs',
			),
			array(
				'id'   => 'products',
				'name' => 'Internal/Products',
			),
			array(
				'id'   => 'taxonomies',
				'name' => 'Taxonomies',
			),
			array(
				'id'   => 'alerts',
				'name' => 'Alerts/Notifications',
			),
			array(
				'id'   => 'media',
				'name' => 'Media',
			),
			array(
				'id'   => 'misc',
				'name' => 'Misc./Post Types',
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
				'name'  => 'Appearance',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-collapse',
				'name'  => 'Collapse',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-comments',
				'name'  => 'Comments',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-customizer',
				'name'  => 'Customizer',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-dashboard',
				'name'  => 'Dashboard',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-generic',
				'name'  => 'Generic',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-filter',
				'name'  => 'Filter',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-home',
				'name'  => 'Home',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-media',
				'name'  => 'Media',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-menu',
				'name'  => 'Menu',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-multisite',
				'name'  => 'Multisite',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-network',
				'name'  => 'Network',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-page',
				'name'  => 'Page',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-plugins',
				'name'  => 'Plugins',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-settings',
				'name'  => 'Settings',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-site',
				'name'  => 'Site',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-tools',
				'name'  => 'Tools',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-admin-users',
				'name'  => 'Users',
				'group' => 'admin',
			),
			array(
				'id'    => 'dashicons-format-standard',
				'name'  => 'Standard',
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-aside',
				'name'  => 'Aside',
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-image',
				'name'  => 'Image',
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-video',
				'name'  => 'Video',
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-audio',
				'name'  => 'Audio',
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-quote',
				'name'  => 'Quote',
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-gallery',
				'name'  => 'Gallery',
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-links',
				'name'  => 'Links',
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-status',
				'name'  => 'Status',
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-format-chat',
				'name'  => 'Chat',
				'group' => 'post-formats',
			),
			array(
				'id'    => 'dashicons-welcome-add-page',
				'name'  => 'Add page',
				'group' => 'welcome-screen',
			),
			array(
				'id'    => 'dashicons-welcome-comments',
				'name'  => 'Comments',
				'group' => 'welcome-screen',
			),
			array(
				'id'    => 'dashicons-welcome-edit-page',
				'name'  => 'Edit page',
				'group' => 'welcome-screen',
			),
			array(
				'id'    => 'dashicons-welcome-learn-more',
				'name'  => 'Learn More',
				'group' => 'welcome-screen',
			),
			array(
				'id'    => 'dashicons-welcome-view-site',
				'name'  => 'View Site',
				'group' => 'welcome-screen',
			),
			array(
				'id'    => 'dashicons-welcome-widgets-menus',
				'name'  => 'Widgets',
				'group' => 'welcome-screen',
			),
			array(
				'id'    => 'dashicons-welcome-write-blog',
				'name'  => 'Write Blog',
				'group' => 'welcome-screen',
			),
			array(
				'id'    => 'dashicons-image-crop',
				'name'  => 'Crop',
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-image-filter',
				'name'  => 'Filter',
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-image-rotate',
				'name'  => 'Rotate',
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-image-rotate-left',
				'name'  => 'Rotate Left',
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-image-rotate-right',
				'name'  => 'Rotate Right',
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-image-flip-vertical',
				'name'  => 'Flip Vertical',
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-image-flip-horizontal',
				'name'  => 'Flip Horizontal',
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-undo',
				'name'  => 'Undo',
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-redo',
				'name'  => 'Redo',
				'group' => 'image-editor',
			),
			array(
				'id'    => 'dashicons-editor-bold',
				'name'  => 'Bold',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-italic',
				'name'  => 'Italic',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-ul',
				'name'  => 'Unordered List',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-ol',
				'name'  => 'Ordered List',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-quote',
				'name'  => 'Quote',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-alignleft',
				'name'  => 'Align Left',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-aligncenter',
				'name'  => 'Align Center',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-alignright',
				'name'  => 'Align Right',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-insertmore',
				'name'  => 'Insert More',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-spellcheck',
				'name'  => 'Spell Check',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-distractionfree',
				'name'  => 'Distraction-free',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-kitchensink',
				'name'  => 'Kitchensink',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-underline',
				'name'  => 'Underline',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-justify',
				'name'  => 'Justify',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-textcolor',
				'name'  => 'Text Color',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-paste-word',
				'name'  => 'Paste Word',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-paste-text',
				'name'  => 'Paste Text',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-removeformatting',
				'name'  => 'Clear Formatting',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-video',
				'name'  => 'Video',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-customchar',
				'name'  => 'Custom Characters',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-indent',
				'name'  => 'Indent',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-outdent',
				'name'  => 'Outdent',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-help',
				'name'  => 'Help',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-strikethrough',
				'name'  => 'Strikethrough',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-unlink',
				'name'  => 'Unlink',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-editor-rtl',
				'name'  => 'RTL',
				'group' => 'text-editor',
			),
			array(
				'id'    => 'dashicons-align-left',
				'name'  => 'Align Left',
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-align-right',
				'name'  => 'Align Right',
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-align-center',
				'name'  => 'Align Center',
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-align-none',
				'name'  => 'Align None',
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-lock',
				'name'  => 'Lock',
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-calendar',
				'name'  => 'Calendar',
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-calendar-alt',
				'name'  => 'Calendar',
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-hidden',
				'name'  => 'Hidden',
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-visibility',
				'name'  => 'Visibility',
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-post-status',
				'name'  => 'Post Status',
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-post-trash',
				'name'  => 'Post Trash',
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-edit',
				'name'  => 'Edit',
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-trash',
				'name'  => 'Trash',
				'group' => 'post',
			),
			array(
				'id'    => 'dashicons-arrow-up',
				'name'  => 'Arrow: Up',
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-down',
				'name'  => 'Arrow: Down',
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-left',
				'name'  => 'Arrow: Left',
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-right',
				'name'  => 'Arrow: Right',
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-up-alt',
				'name'  => 'Arrow: Up',
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-down-alt',
				'name'  => 'Arrow: Down',
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-left-alt',
				'name'  => 'Arrow: Left',
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-right-alt',
				'name'  => 'Arrow: Right',
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-up-alt2',
				'name'  => 'Arrow: Up',
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-down-alt2',
				'name'  => 'Arrow: Down',
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-left-alt2',
				'name'  => 'Arrow: Left',
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-arrow-right-alt2',
				'name'  => 'Arrow: Right',
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-leftright',
				'name'  => 'Left-Right',
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-sort',
				'name'  => 'Sort',
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-list-view',
				'name'  => 'List View',
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-exerpt-view',
				'name'  => 'Excerpt View',
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-grid-view',
				'name'  => 'Grid View',
				'group' => 'sorting',
			),
			array(
				'id'    => 'dashicons-share',
				'name'  => 'Share',
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-share-alt',
				'name'  => 'Share',
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-share-alt2',
				'name'  => 'Share',
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-twitter',
				'name'  => 'Twitter',
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-rss',
				'name'  => 'RSS',
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-email',
				'name'  => 'Email',
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-email-alt',
				'name'  => 'Email',
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-facebook',
				'name'  => 'Facebook',
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-facebook-alt',
				'name'  => 'Facebook',
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-googleplus',
				'name'  => 'Google+',
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-networking',
				'name'  => 'Networking',
				'group' => 'social',
			),
			array(
				'id'    => 'dashicons-art',
				'name'  => 'Art',
				'group' => 'jobs',
			),
			array(
				'id'    => 'dashicons-hammer',
				'name'  => 'Hammer',
				'group' => 'jobs',
			),
			array(
				'id'    => 'dashicons-migrate',
				'name'  => 'Migrate',
				'group' => 'jobs',
			),
			array(
				'id'    => 'dashicons-performance',
				'name'  => 'Performance',
				'group' => 'jobs',
			),
			array(
				'id'    => 'dashicons-wordpress',
				'name'  => 'WordPress',
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-wordpress-alt',
				'name'  => 'WordPress',
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-pressthis',
				'name'  => 'PressThis',
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-update',
				'name'  => 'Update',
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-screenoptions',
				'name'  => 'Screen Options',
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-info',
				'name'  => 'Info',
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-cart',
				'name'  => 'Cart',
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-feedback',
				'name'  => 'Feedback',
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-cloud',
				'name'  => 'Cloud',
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-translation',
				'name'  => 'Translation',
				'group' => 'products',
			),
			array(
				'id'    => 'dashicons-tag',
				'name'  => 'Tag',
				'group' => 'taxonomies',
			),
			array(
				'id'    => 'dashicons-category',
				'name'  => 'Category',
				'group' => 'taxonomies',
			),
			array(
				'id'    => 'dashicons-yes',
				'name'  => 'Yes',
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-no',
				'name'  => 'No',
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-no-alt',
				'name'  => 'No',
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-plus',
				'name'  => 'Plus',
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-minus',
				'name'  => 'Minus',
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-dismiss',
				'name'  => 'Dismiss',
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-marker',
				'name'  => 'Marker',
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-star-filled',
				'name'  => 'Star: Filled',
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-star-half',
				'name'  => 'Star: Half',
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-star-empty',
				'name'  => 'Star: Empty',
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-flag',
				'name'  => 'Flag',
				'group' => 'alerts',
			),
			array(
				'id'    => 'dashicons-controls-skipback',
				'name'  => 'Skip Back',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-controls-back',
				'name'  => 'Back',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-controls-play',
				'name'  => 'Play',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-controls-pause',
				'name'  => 'Pause',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-controls-forward',
				'name'  => 'Forward',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-controls-skipforward',
				'name'  => 'Skip Forward',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-controls-repeat',
				'name'  => 'Repeat',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-controls-volumeon',
				'name'  => 'Volume: On',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-controls-volumeoff',
				'name'  => 'Volume: Off',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-archive',
				'name'  => 'Archive',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-audio',
				'name'  => 'Audio',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-code',
				'name'  => 'Code',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-default',
				'name'  => 'Default',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-document',
				'name'  => 'Document',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-interactive',
				'name'  => 'Interactive',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-spreadsheet',
				'name'  => 'Spreadsheet',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-text',
				'name'  => 'Text',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-media-video',
				'name'  => 'Video',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-playlist-audio',
				'name'  => 'Audio Playlist',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-playlist-video',
				'name'  => 'Video Playlist',
				'group' => 'media',
			),
			array(
				'id'    => 'dashicons-album',
				'name'  => 'Album',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-analytics',
				'name'  => 'Analytics',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-awards',
				'name'  => 'Awards',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-backup',
				'name'  => 'Backup',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-building',
				'name'  => 'Building',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-businessman',
				'name'  => 'Businessman',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-camera',
				'name'  => 'Camera',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-carrot',
				'name'  => 'Carrot',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-chart-pie',
				'name'  => 'Chart: Pie',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-chart-bar',
				'name'  => 'Chart: Bar',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-chart-line',
				'name'  => 'Chart: Line',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-chart-area',
				'name'  => 'Chart: Area',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-desktop',
				'name'  => 'Desktop',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-forms',
				'name'  => 'Forms',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-groups',
				'name'  => 'Groups',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-id',
				'name'  => 'ID',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-id-alt',
				'name'  => 'ID',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-images-alt',
				'name'  => 'Images',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-images-alt2',
				'name'  => 'Images',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-index-card',
				'name'  => 'Index Card',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-layout',
				'name'  => 'Layout',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-location',
				'name'  => 'Location',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-location-alt',
				'name'  => 'Location',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-products',
				'name'  => 'Products',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-portfolio',
				'name'  => 'Portfolio',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-book',
				'name'  => 'Book',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-book-alt',
				'name'  => 'Book',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-download',
				'name'  => 'Download',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-upload',
				'name'  => 'Upload',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-clock',
				'name'  => 'Clock',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-lightbulb',
				'name'  => 'Lightbulb',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-money',
				'name'  => 'Money',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-palmtree',
				'name'  => 'Palm Tree',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-phone',
				'name'  => 'Phone',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-search',
				'name'  => 'Search',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-shield',
				'name'  => 'Shield',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-shield-alt',
				'name'  => 'Shield',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-slides',
				'name'  => 'Slides',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-smartphone',
				'name'  => 'Smartphone',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-smiley',
				'name'  => 'Smiley',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-sos',
				'name'  => 'S.O.S.',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-sticky',
				'name'  => 'Sticky',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-store',
				'name'  => 'Store',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-tablet',
				'name'  => 'Tablet',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-testimonial',
				'name'  => 'Testimonial',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-tickets-alt',
				'name'  => 'Tickets',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-thumbs-up',
				'name'  => 'Thumbs Up',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-thumbs-down',
				'name'  => 'Thumbs Down',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-unlock',
				'name'  => 'Unlock',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-vault',
				'name'  => 'Vault',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-video-alt',
				'name'  => 'Video',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-video-alt2',
				'name'  => 'Video',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-video-alt3',
				'name'  => 'Video',
				'group' => 'misc',
			),
			array(
				'id'    => 'dashicons-warning',
				'name'  => 'Warning',
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
