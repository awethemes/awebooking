<?php
namespace Skeleton\Iconfonts\Icons;

class FontAwesome extends Iconpack {
	/**
	 * Iconpack unique ID.
	 *
	 * @var string
	 */
	public $id = 'fa';

	/**
	 * Iconpack display name.
	 *
	 * @var string
	 */
	public $name = 'Font Awesome';

	/**
	 * Iconpack version.
	 *
	 * @var string
	 */
	public $version = '4.6.1';

	/**
	 * Stylesheet ID.
	 *
	 * @var string
	 */
	public $stylesheet_id = 'font-awesome';

	/**
	 * Stylesheet URI.
	 *
	 * @var string
	 */
	public $stylesheet_uri = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.1/css/font-awesome.min.css';

	/**
	 * Return an array icon groups.
	 *
	 * @return array
	 */
	public function groups() {
		$groups = array(
			array(
				'id'   => 'a11y',
				'name' => 'Accessibility',
			),
			array(
				'id'   => 'brand',
				'name' => 'Brand',
			),
			array(
				'id'   => 'chart',
				'name' => 'Charts',
			),
			array(
				'id'   => 'currency',
				'name' => 'Currency',
			),
			array(
				'id'   => 'directional',
				'name' => 'Directional',
			),
			array(
				'id'   => 'file-types',
				'name' => 'File Types',
			),
			array(
				'id'   => 'form-control',
				'name' => 'Form Controls',
			),
			array(
				'id'   => 'gender',
				'name' => 'Genders',
			),
			array(
				'id'   => 'medical',
				'name' => 'Medical',
			),
			array(
				'id'   => 'payment',
				'name' => 'Payment',
			),
			array(
				'id'   => 'spinner',
				'name' => 'Spinners',
			),
			array(
				'id'   => 'transportation',
				'name' => 'Transportation',
			),
			array(
				'id'   => 'text-editor',
				'name' => 'Text Editor',
			),
			array(
				'id'   => 'video-player',
				'name' => 'Video Player',
			),
			array(
				'id'   => 'web-application',
				'name' => 'Web Application',
			),
		);

		/**
		 * Filter dashicon groups
		 *
		 * @param array $groups Icon groups.
		 */
		$groups = apply_filters( 'skeleton/iconfonts/fontawesome/group', $groups );

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
				'group' => 'a11y',
				'id'    => ' fa-american-sign-language-interpreting',
				'name'  => 'American Sign Language',
			),
			array(
				'group' => 'a11y',
				'id'    => ' fa-audio-description',
				'name'  => 'Audio Description',
			),
			array(
				'group' => 'a11y',
				'id'    => ' fa-assistive-listening-systems',
				'name'  => 'Assistive Listening Systems',
			),
			array(
				'group' => 'a11y',
				'id'    => 'fa-blind',
				'name'  => 'Blind',
			),
			array(
				'group' => 'a11y',
				'id'    => 'fa-braille',
				'name'  => 'Braille',
			),
			array(
				'group' => 'a11y',
				'id'    => 'fa-deaf',
				'name'  => 'Deaf',
			),
			array(
				'group' => 'a11y',
				'id'    => 'fa-low-vision',
				'name'  => 'Low Vision',
			),
			array(
				'group' => 'a11y',
				'id'    => 'fa-volume-control-phone',
				'name'  => 'Phone Volume Control',
			),
			array(
				'group' => 'a11y',
				'id'    => 'fa-sign-language',
				'name'  => 'Sign Language',
			),
			array(
				'group' => 'a11y',
				'id'    => 'fa-universal-access',
				'name'  => 'Universal Access',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-500px',
				'name'  => '500px',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-adn',
				'name'  => 'ADN',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-amazon',
				'name'  => 'Amazon',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-android',
				'name'  => 'Android',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-angellist',
				'name'  => 'AngelList',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-apple',
				'name'  => 'Apple',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-black-tie',
				'name'  => 'BlackTie',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-behance',
				'name'  => 'Behance',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-behance-square',
				'name'  => 'Behance',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-bitbucket',
				'name'  => 'Bitbucket',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-bluetooth',
				'name'  => 'Bluetooth',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-bluetooth-b',
				'name'  => 'Bluetooth',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-bitbucket-square',
				'name'  => 'Bitbucket',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-buysellads',
				'name'  => 'BuySellAds',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-chrome',
				'name'  => 'Chrome',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-codepen',
				'name'  => 'CodePen',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-codiepie',
				'name'  => 'Codie Pie',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-connectdevelop',
				'name'  => 'Connect + Develop',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-contao',
				'name'  => 'Contao',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-creative-commons',
				'name'  => 'Creative Commons',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-css3',
				'name'  => 'CSS3',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-dashcube',
				'name'  => 'Dashcube',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-delicious',
				'name'  => 'Delicious',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-deviantart',
				'name'  => 'deviantART',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-digg',
				'name'  => 'Digg',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-dribbble',
				'name'  => 'Dribbble',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-dropbox',
				'name'  => 'DropBox',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-drupal',
				'name'  => 'Drupal',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-empire',
				'name'  => 'Empire',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-edge',
				'name'  => 'Edge',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-envira',
				'name'  => 'Envira',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-expeditedssl',
				'name'  => 'ExpeditedSSL',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-facebook-official',
				'name'  => 'Facebook',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-facebook-square',
				'name'  => 'Facebook',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-facebook',
				'name'  => 'Facebook',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-firefox',
				'name'  => 'Firefox',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-flickr',
				'name'  => 'Flickr',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-fonticons',
				'name'  => 'FontIcons',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-fort-awesome',
				'name'  => 'Fort Awesome',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-forumbee',
				'name'  => 'Forumbee',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-foursquare',
				'name'  => 'Foursquare',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-get-pocket',
				'name'  => 'Pocket',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-git',
				'name'  => 'Git',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-git-square',
				'name'  => 'Git',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-github',
				'name'  => 'GitHub',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-gitlab',
				'name'  => 'Gitlab',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-github-alt',
				'name'  => 'GitHub',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-github-square',
				'name'  => 'GitHub',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-gittip',
				'name'  => 'GitTip',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-glide',
				'name'  => 'Glide',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-glide-g',
				'name'  => 'Glide',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-google',
				'name'  => 'Google',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-google-plus',
				'name'  => 'Google+',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-google-plus-square',
				'name'  => 'Google+',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-hacker-news',
				'name'  => 'Hacker News',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-houzz',
				'name'  => 'Houzz',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-html5',
				'name'  => 'HTML5',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-instagram',
				'name'  => 'Instagram',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-internet-explorer',
				'name'  => 'Internet Explorer',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-ioxhost',
				'name'  => 'IoxHost',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-joomla',
				'name'  => 'Joomla',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-jsfiddle',
				'name'  => 'JSFiddle',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-lastfm',
				'name'  => 'Last.fm',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-lastfm-square',
				'name'  => 'Last.fm',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-leanpub',
				'name'  => 'Leanpub',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-linkedin',
				'name'  => 'LinkedIn',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-linkedin-square',
				'name'  => 'LinkedIn',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-linux',
				'name'  => 'Linux',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-maxcdn',
				'name'  => 'MaxCDN',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-meanpath',
				'name'  => 'meanpath',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-medium',
				'name'  => 'Medium',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-mixcloud',
				'name'  => 'Mixcloud',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-modx',
				'name'  => 'MODX',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-odnoklassniki',
				'name'  => 'Odnoklassniki',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-odnoklassniki-square',
				'name'  => 'Odnoklassniki',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-opencart',
				'name'  => 'OpenCart',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-openid',
				'name'  => 'OpenID',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-opera',
				'name'  => 'Opera',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-optin-monster',
				'name'  => 'OptinMonster',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-pagelines',
				'name'  => 'Pagelines',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-pied-piper',
				'name'  => 'Pied Piper',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-pied-piper-alt',
				'name'  => 'Pied Piper',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-pinterest',
				'name'  => 'Pinterest',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-pinterest-p',
				'name'  => 'Pinterest',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-pinterest-square',
				'name'  => 'Pinterest',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-product-hunt',
				'name'  => 'Product Hunt',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-qq',
				'name'  => 'QQ',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-reddit',
				'name'  => 'reddit',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-reddit-alien',
				'name'  => 'reddit',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-reddit-square',
				'name'  => 'reddit',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-renren',
				'name'  => 'Renren',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-safari',
				'name'  => 'Safari',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-scribd',
				'name'  => 'Scribd',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-sellsy',
				'name'  => 'SELLSY',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-shirtsinbulk',
				'name'  => 'Shirts In Bulk',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-simplybuilt',
				'name'  => 'SimplyBuilt',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-skyatlas',
				'name'  => 'Skyatlas',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-skype',
				'name'  => 'Skype',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-slack',
				'name'  => 'Slack',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-slideshare',
				'name'  => 'SlideShare',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-soundcloud',
				'name'  => 'SoundCloud',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-snapchat',
				'name'  => 'Snapchat',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-snapchat-ghost',
				'name'  => 'Snapchat',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-snapchat-square',
				'name'  => 'Snapchat',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-spotify',
				'name'  => 'Spotify',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-stack-exchange',
				'name'  => 'Stack Exchange',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-stack-overflow',
				'name'  => 'Stack Overflow',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-steam',
				'name'  => 'Steam',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-steam-square',
				'name'  => 'Steam',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-stumbleupon',
				'name'  => 'StumbleUpon',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-stumbleupon-circle',
				'name'  => 'StumbleUpon',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-tencent-weibo',
				'name'  => 'Tencent Weibo',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-trello',
				'name'  => 'Trello',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-tripadvisor',
				'name'  => 'TripAdvisor',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-tumblr',
				'name'  => 'Tumblr',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-tumblr-square',
				'name'  => 'Tumblr',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-twitch',
				'name'  => 'Twitch',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-twitter',
				'name'  => 'Twitter',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-twitter-square',
				'name'  => 'Twitter',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-usb',
				'name'  => 'USB',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-vimeo',
				'name'  => 'Vimeo',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-viadeo',
				'name'  => 'Viadeo',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-viadeo-square',
				'name'  => 'Viadeo',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-vimeo-square',
				'name'  => 'Vimeo',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-viacoin',
				'name'  => 'Viacoin',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-vine',
				'name'  => 'Vine',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-vk',
				'name'  => 'VK',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-weixin',
				'name'  => 'Weixin',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-weibo',
				'name'  => 'Wibo',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-whatsapp',
				'name'  => 'WhatsApp',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-wikipedia-w',
				'name'  => 'Wikipedia',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-windows',
				'name'  => 'Windows',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-wordpress',
				'name'  => 'WordPress',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-wpbeginner',
				'name'  => 'WP Beginner',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-wpforms',
				'name'  => 'WP Forms',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-xing',
				'name'  => 'Xing',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-xing-square',
				'name'  => 'Xing',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-y-combinator',
				'name'  => 'Y Combinator',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-yahoo',
				'name'  => 'Yahoo!',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-yelp',
				'name'  => 'Yelp',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-youtube',
				'name'  => 'YouTube',
			),
			array(
				'group' => 'brand',
				'id'    => 'fa-youtube-square',
				'name'  => 'YouTube',
			),
			array(
				'group' => 'chart',
				'id'    => 'fa-area-chart',
				'name'  => 'Area Chart',
			),
			array(
				'group' => 'chart',
				'id'    => 'fa-bar-chart-o',
				'name'  => 'Bar Chart',
			),
			array(
				'group' => 'chart',
				'id'    => 'fa-line-chart',
				'name'  => 'Line Chart',
			),
			array(
				'group' => 'chart',
				'id'    => 'fa-pie-chart',
				'name'  => 'Pie Chart',
			),
			array(
				'group' => 'currency',
				'id'    => 'fa-bitcoin',
				'name'  => 'Bitcoin',
			),
			array(
				'group' => 'currency',
				'id'    => 'fa-dollar',
				'name'  => 'Dollar',
			),
			array(
				'group' => 'currency',
				'id'    => 'fa-euro',
				'name'  => 'Euro',
			),
			array(
				'group' => 'currency',
				'id'    => 'fa-gbp',
				'name'  => 'GBP',
			),
			array(
				'group' => 'currency',
				'id'    => 'fa-gg',
				'name'  => 'GBP',
			),
			array(
				'group' => 'currency',
				'id'    => 'fa-gg-circle',
				'name'  => 'GG',
			),
			array(
				'group' => 'currency',
				'id'    => 'fa-ils',
				'name'  => 'Israeli Sheqel',
			),
			array(
				'group' => 'currency',
				'id'    => 'fa-money',
				'name'  => 'Money',
			),
			array(
				'group' => 'currency',
				'id'    => 'fa-rouble',
				'name'  => 'Rouble',
			),
			array(
				'group' => 'currency',
				'id'    => 'fa-inr',
				'name'  => 'Rupee',
			),
			array(
				'group' => 'currency',
				'id'    => 'fa-try',
				'name'  => 'Turkish Lira',
			),
			array(
				'group' => 'currency',
				'id'    => 'fa-krw',
				'name'  => 'Won',
			),
			array(
				'group' => 'currency',
				'id'    => 'fa-jpy',
				'name'  => 'Yen',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-angle-down',
				'name'  => 'Angle Down',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-angle-left',
				'name'  => 'Angle Left',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-angle-right',
				'name'  => 'Angle Right',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-angle-up',
				'name'  => 'Angle Up',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-angle-double-down',
				'name'  => 'Angle Double Down',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-angle-double-left',
				'name'  => 'Angle Double Left',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-angle-double-right',
				'name'  => 'Angle Double Right',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-angle-double-up',
				'name'  => 'Angle Double Up',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-arrow-circle-o-down',
				'name'  => 'Arrow Circle Down',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-arrow-circle-o-left',
				'name'  => 'Arrow Circle Left',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-arrow-circle-o-right',
				'name'  => 'Arrow Circle Right',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-arrow-circle-o-up',
				'name'  => 'Arrow Circle Up',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-arrow-circle-down',
				'name'  => 'Arrow Circle Down',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-arrow-circle-left',
				'name'  => 'Arrow Circle Left',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-arrow-circle-right',
				'name'  => 'Arrow Circle Right',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-arrow-circle-up',
				'name'  => 'Arrow Circle Up',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-arrow-down',
				'name'  => 'Arrow Down',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-arrow-left',
				'name'  => 'Arrow Left',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-arrow-right',
				'name'  => 'Arrow Right',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-arrow-up',
				'name'  => 'Arrow Up',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-arrows',
				'name'  => 'Arrows',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-arrows-alt',
				'name'  => 'Arrows',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-arrows-h',
				'name'  => 'Arrows',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-arrows-v',
				'name'  => 'Arrows',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-caret-down',
				'name'  => 'Caret Down',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-caret-left',
				'name'  => 'Caret Left',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-caret-right',
				'name'  => 'Caret Right',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-caret-up',
				'name'  => 'Caret Up',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-caret-square-o-down',
				'name'  => 'Caret Down',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-caret-square-o-left',
				'name'  => 'Caret Left',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-caret-square-o-right',
				'name'  => 'Caret Right',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-caret-square-o-up',
				'name'  => 'Caret Up',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-chevron-circle-down',
				'name'  => 'Chevron Circle Down',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-chevron-circle-left',
				'name'  => 'Chevron Circle Left',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-chevron-circle-right',
				'name'  => 'Chevron Circle Right',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-chevron-circle-up',
				'name'  => 'Chevron Circle Up',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-chevron-down',
				'name'  => 'Chevron Down',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-chevron-left',
				'name'  => 'Chevron Left',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-chevron-right',
				'name'  => 'Chevron Right',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-chevron-up',
				'name'  => 'Chevron Up',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-hand-o-down',
				'name'  => 'Hand Down',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-hand-o-left',
				'name'  => 'Hand Left',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-hand-o-right',
				'name'  => 'Hand Right',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-hand-o-up',
				'name'  => 'Hand Up',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-long-arrow-down',
				'name'  => 'Long Arrow Down',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-long-arrow-left',
				'name'  => 'Long Arrow Left',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-long-arrow-right',
				'name'  => 'Long Arrow Right',
			),
			array(
				'group' => 'directional',
				'id'    => 'fa-long-arrow-up',
				'name'  => 'Long Arrow Up',
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa-file',
				'name'  => 'File',
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa-file-o',
				'name'  => 'File',
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa-file-text',
				'name'  => 'File: Text',
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa-file-text-o',
				'name'  => 'File: Text',
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa-file-archive-o',
				'name'  => 'File: Archive',
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa-file-audio-o',
				'name'  => 'File: Audio',
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa-file-code-o',
				'name'  => 'File: Code',
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa-file-excel-o',
				'name'  => 'File: Excel',
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa-file-image-o',
				'name'  => 'File: Image',
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa-file-pdf-o',
				'name'  => 'File: PDF',
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa-file-powerpoint-o',
				'name'  => 'File: Powerpoint',
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa-file-video-o',
				'name'  => 'File: Video',
			),
			array(
				'group' => 'file-types',
				'id'    => 'fa-file-word-o',
				'name'  => 'File: Word',
			),
			array(
				'group' => 'form-control',
				'id'    => 'fa-check-square',
				'name'  => 'Check',
			),
			array(
				'group' => 'form-control',
				'id'    => 'fa-check-square-o',
				'name'  => 'Check',
			),
			array(
				'group' => 'form-control',
				'id'    => 'fa-circle',
				'name'  => 'Circle',
			),
			array(
				'group' => 'form-control',
				'id'    => 'fa-circle-o',
				'name'  => 'Circle',
			),
			array(
				'group' => 'form-control',
				'id'    => 'fa-dot-circle-o',
				'name'  => 'Dot',
			),
			array(
				'group' => 'form-control',
				'id'    => 'fa-minus-square',
				'name'  => 'Minus',
			),
			array(
				'group' => 'form-control',
				'id'    => 'fa-minus-square-o',
				'name'  => 'Minus',
			),
			array(
				'group' => 'form-control',
				'id'    => 'fa-plus-square',
				'name'  => 'Plus',
			),
			array(
				'group' => 'form-control',
				'id'    => 'fa-plus-square-o',
				'name'  => 'Plus',
			),
			array(
				'group' => 'form-control',
				'id'    => 'fa-square',
				'name'  => 'Square',
			),
			array(
				'group' => 'form-control',
				'id'    => 'fa-square-o',
				'name'  => 'Square',
			),
			array(
				'group' => 'gender',
				'id'    => 'fa-genderless',
				'name'  => 'Genderless',
			),
			array(
				'group' => 'gender',
				'id'    => 'fa-mars',
				'name'  => 'Mars',
			),
			array(
				'group' => 'gender',
				'id'    => 'fa-mars-double',
				'name'  => 'Mars',
			),
			array(
				'group' => 'gender',
				'id'    => 'fa-mars-stroke',
				'name'  => 'Mars',
			),
			array(
				'group' => 'gender',
				'id'    => 'fa-mars-stroke-h',
				'name'  => 'Mars',
			),
			array(
				'group' => 'gender',
				'id'    => 'fa-mars-stroke-v',
				'name'  => 'Mars',
			),
			array(
				'group' => 'gender',
				'id'    => 'fa-mercury',
				'name'  => 'Mercury',
			),
			array(
				'group' => 'gender',
				'id'    => 'fa-neuter',
				'name'  => 'Neuter',
			),
			array(
				'group' => 'gender',
				'id'    => 'fa-transgender',
				'name'  => 'Transgender',
			),
			array(
				'group' => 'gender',
				'id'    => 'fa-transgender-alt',
				'name'  => 'Transgender',
			),
			array(
				'group' => 'gender',
				'id'    => 'fa-venus',
				'name'  => 'Venus',
			),
			array(
				'group' => 'gender',
				'id'    => 'fa-venus-double',
				'name'  => 'Venus',
			),
			array(
				'group' => 'gender',
				'id'    => 'fa-venus-mars',
				'name'  => 'Venus + Mars',
			),
			array(
				'group' => 'medical',
				'id'    => 'fa-heart',
				'name'  => 'Heart',
			),
			array(
				'group' => 'medical',
				'id'    => 'fa-heart-o',
				'name'  => 'Heart',
			),
			array(
				'group' => 'medical',
				'id'    => 'fa-heartbeat',
				'name'  => 'Heartbeat',
			),
			array(
				'group' => 'medical',
				'id'    => 'fa-h-square',
				'name'  => 'Hospital',
			),
			array(
				'group' => 'medical',
				'id'    => 'fa-hospital-o',
				'name'  => 'Hospital',
			),
			array(
				'group' => 'medical',
				'id'    => 'fa-medkit',
				'name'  => 'Medkit',
			),
			array(
				'group' => 'medical',
				'id'    => 'fa-stethoscope',
				'name'  => 'Stethoscope',
			),
			array(
				'group' => 'medical',
				'id'    => 'fa-user-md',
				'name'  => 'User MD',
			),
			array(
				'group' => 'payment',
				'id'    => 'fa-cc-amex',
				'name'  => 'American Express',
			),
			array(
				'group' => 'payment',
				'id'    => 'fa-credit-card',
				'name'  => 'Credit Card',
			),
			array(
				'group' => 'payment',
				'id'    => 'fa-credit-card-alt',
				'name'  => 'Credit Card',
			),
			array(
				'group' => 'payment',
				'id'    => 'fa-cc-diners-club',
				'name'  => 'Diners Club',
			),
			array(
				'group' => 'payment',
				'id'    => 'fa-cc-discover',
				'name'  => 'Discover',
			),
			array(
				'group' => 'payment',
				'id'    => 'fa-google-wallet',
				'name'  => 'Google Wallet',
			),
			array(
				'group' => 'payment',
				'id'    => 'fa-cc-jcb',
				'name'  => 'JCB',
			),
			array(
				'group' => 'payment',
				'id'    => 'fa-cc-mastercard',
				'name'  => 'MasterCard',
			),
			array(
				'group' => 'payment',
				'id'    => 'fa-cc-paypal',
				'name'  => 'PayPal',
			),
			array(
				'group' => 'payment',
				'id'    => 'fa-paypal',
				'name'  => 'PayPal',
			),
			array(
				'group' => 'payment',
				'id'    => 'fa-cc-stripe',
				'name'  => 'Stripe',
			),
			array(
				'group' => 'payment',
				'id'    => 'fa-cc-visa',
				'name'  => 'Visa',
			),
			array(
				'group' => 'spinner',
				'id'    => 'fa-circle-o-notch',
				'name'  => 'Circle',
			),
			array(
				'group' => 'spinner',
				'id'    => 'fa-cog',
				'name'  => 'Cog',
			),
			array(
				'group' => 'spinner',
				'id'    => 'fa-refresh',
				'name'  => 'Refresh',
			),
			array(
				'group' => 'spinner',
				'id'    => 'fa-spinner',
				'name'  => 'Spinner',
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa-ambulance',
				'name'  => 'Ambulance',
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa-bicycle',
				'name'  => 'Bicycle',
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa-bus',
				'name'  => 'Bus',
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa-car',
				'name'  => 'Car',
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa-fighter-jet',
				'name'  => 'Fighter Jet',
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa-motorcycle',
				'name'  => 'Motorcycle',
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa-plane',
				'name'  => 'Plane',
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa-rocket',
				'name'  => 'Rocket',
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa-ship',
				'name'  => 'Ship',
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa-space-shuttle',
				'name'  => 'Space Shuttle',
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa-subway',
				'name'  => 'Subway',
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa-taxi',
				'name'  => 'Taxi',
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa-train',
				'name'  => 'Train',
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa-truck',
				'name'  => 'Truck',
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa-wheelchair',
				'name'  => 'Wheelchair',
			),
			array(
				'group' => 'transportation',
				'id'    => 'fa-wheelchair-alt',
				'name'  => 'Wheelchair',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-align-left',
				'name'  => 'Align Left',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-align-center',
				'name'  => 'Align Center',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-align-justify',
				'name'  => 'Justify',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-align-right',
				'name'  => 'Align Right',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-bold',
				'name'  => 'Bold',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-clipboard',
				'name'  => 'Clipboard',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-columns',
				'name'  => 'Columns',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-copy',
				'name'  => 'Copy',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-cut',
				'name'  => 'Cut',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-paste',
				'name'  => 'Paste',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-eraser',
				'name'  => 'Eraser',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-files-o',
				'name'  => 'Files',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-font',
				'name'  => 'Font',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-header',
				'name'  => 'Header',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-indent',
				'name'  => 'Indent',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-outdent',
				'name'  => 'Outdent',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-italic',
				'name'  => 'Italic',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-link',
				'name'  => 'Link',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-unlink',
				'name'  => 'Unlink',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-list',
				'name'  => 'List',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-list-alt',
				'name'  => 'List',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-list-ol',
				'name'  => 'Ordered List',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-list-ul',
				'name'  => 'Unordered List',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-paperclip',
				'name'  => 'Paperclip',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-paragraph',
				'name'  => 'Paragraph',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-repeat',
				'name'  => 'Repeat',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-undo',
				'name'  => 'Undo',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-save',
				'name'  => 'Save',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-strikethrough',
				'name'  => 'Strikethrough',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-subscript',
				'name'  => 'Subscript',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-superscript',
				'name'  => 'Superscript',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-table',
				'name'  => 'Table',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-text-height',
				'name'  => 'Text Height',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-text-width',
				'name'  => 'Text Width',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-th',
				'name'  => 'Table Header',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-th-large',
				'name'  => 'TH Large',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-th-list',
				'name'  => 'TH List',
			),
			array(
				'group' => 'text-editor',
				'id'    => 'fa-underline',
				'name'  => 'Underline',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-arrows-alt',
				'name'  => 'Arrows',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-backward',
				'name'  => 'Backward',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-compress',
				'name'  => 'Compress',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-eject',
				'name'  => 'Eject',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-expand',
				'name'  => 'Expand',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-fast-backward',
				'name'  => 'Fast Backward',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-fast-forward',
				'name'  => 'Fast Forward',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-forward',
				'name'  => 'Forward',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-pause',
				'name'  => 'Pause',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-pause-circle',
				'name'  => 'Pause',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-pause-circle-o',
				'name'  => 'Pause',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-play',
				'name'  => 'Play',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-play-circle',
				'name'  => 'Play',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-play-circle-o',
				'name'  => 'Play',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-step-backward',
				'name'  => 'Step Backward',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-step-forward',
				'name'  => 'Step Forward',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-stop',
				'name'  => 'Stop',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-stop-circle',
				'name'  => 'Stop',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-stop-circle-o',
				'name'  => 'Stop',
			),
			array(
				'group' => 'video-player',
				'id'    => 'fa-youtube-play',
				'name'  => 'YouTube Play',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-adjust',
				'name'  => 'Adjust',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-anchor',
				'name'  => 'Anchor',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-archive',
				'name'  => 'Archive',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-arrows',
				'name'  => 'Arrows',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-arrows-h',
				'name'  => 'Arrows',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-arrows-v',
				'name'  => 'Arrows',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-asterisk',
				'name'  => 'Asterisk',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-at',
				'name'  => 'At',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-balance-scale',
				'name'  => 'Balance',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-ban',
				'name'  => 'Ban',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-barcode',
				'name'  => 'Barcode',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-bars',
				'name'  => 'Bars',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-battery-empty',
				'name'  => 'Battery',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-battery-quarter',
				'name'  => 'Battery',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-battery-half',
				'name'  => 'Battery',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-battery-full',
				'name'  => 'Battery',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-bed',
				'name'  => 'Bed',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-beer',
				'name'  => 'Beer',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-bell',
				'name'  => 'Bell',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-bell-o',
				'name'  => 'Bell',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-bell-slash',
				'name'  => 'Bell',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-bell-slash-o',
				'name'  => 'Bell',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-binoculars',
				'name'  => 'Binoculars',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-birthday-cake',
				'name'  => 'Birthday Cake',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-bolt',
				'name'  => 'Bolt',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-book',
				'name'  => 'Book',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-bookmark',
				'name'  => 'Bookmark',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-bookmark-o',
				'name'  => 'Bookmark',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-bomb',
				'name'  => 'Bomb',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-briefcase',
				'name'  => 'Briefcase',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-bug',
				'name'  => 'Bug',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-building',
				'name'  => 'Building',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-building-o',
				'name'  => 'Building',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-bullhorn',
				'name'  => 'Bullhorn',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-bullseye',
				'name'  => 'Bullseye',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-calculator',
				'name'  => 'Calculator',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-calendar',
				'name'  => 'Calendar',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-calendar-o',
				'name'  => 'Calendar',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-calendar-check-o',
				'name'  => 'Calendar',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-calendar-minus-o',
				'name'  => 'Calendar',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-calendar-times-o',
				'name'  => 'Calendar',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-camera',
				'name'  => 'Camera',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-camera-retro',
				'name'  => 'Camera Retro',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-caret-square-o-down',
				'name'  => 'Caret Down',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-caret-square-o-left',
				'name'  => 'Caret Left',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-caret-square-o-right',
				'name'  => 'Caret Right',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-caret-square-o-up',
				'name'  => 'Caret Up',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-cart-arrow-down',
				'name'  => 'Cart Arrow Down',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-cart-plus',
				'name'  => 'Cart Plus',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-certificate',
				'name'  => 'Certificate',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-check',
				'name'  => 'Check',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-check-circle',
				'name'  => 'Check',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-check-circle-o',
				'name'  => 'Check',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-child',
				'name'  => 'Child',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-circle-thin',
				'name'  => 'Circle',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-clock-o',
				'name'  => 'Clock',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-clone',
				'name'  => 'Clone',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-cloud',
				'name'  => 'Cloud',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-cloud-download',
				'name'  => 'Cloud Download',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-cloud-upload',
				'name'  => 'Cloud Upload',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-code',
				'name'  => 'Code',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-code-fork',
				'name'  => 'Code Fork',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-coffee',
				'name'  => 'Coffee',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-cogs',
				'name'  => 'Cogs',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-comment',
				'name'  => 'Comment',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-comment-o',
				'name'  => 'Comment',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-comments',
				'name'  => 'Comments',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-comments-o',
				'name'  => 'Comments',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-commenting',
				'name'  => 'Commenting',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-commenting-o',
				'name'  => 'Commenting',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-compass',
				'name'  => 'Compass',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-copyright',
				'name'  => 'Copyright',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-credit-card',
				'name'  => 'Credit Card',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-crop',
				'name'  => 'Crop',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-crosshairs',
				'name'  => 'Crosshairs',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-cube',
				'name'  => 'Cube',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-cubes',
				'name'  => 'Cubes',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-i-cursor',
				'name'  => 'Cursor',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-cutlery',
				'name'  => 'Cutlery',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-dashboard',
				'name'  => 'Dashboard',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-database',
				'name'  => 'Database',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-desktop',
				'name'  => 'Desktop',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-diamond',
				'name'  => 'Diamond',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-download',
				'name'  => 'Download',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-edit',
				'name'  => 'Edit',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-ellipsis-h',
				'name'  => 'Ellipsis',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-ellipsis-v',
				'name'  => 'Ellipsis',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-envelope',
				'name'  => 'Envelope',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-envelope-o',
				'name'  => 'Envelope',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-envelope-square',
				'name'  => 'Envelope',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-eraser',
				'name'  => 'Eraser',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-exchange',
				'name'  => 'Exchange',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-exclamation',
				'name'  => 'Exclamation',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-exclamation-circle',
				'name'  => 'Exclamation',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-exclamation-triangle',
				'name'  => 'Exclamation',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-external-link',
				'name'  => 'External Link',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-external-link-square',
				'name'  => 'External Link',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-eye',
				'name'  => 'Eye',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-eye-slash',
				'name'  => 'Eye',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-eyedropper',
				'name'  => 'Eye Dropper',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-fax',
				'name'  => 'Fax',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-female',
				'name'  => 'Female',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-film',
				'name'  => 'Film',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-filter',
				'name'  => 'Filter',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-fire',
				'name'  => 'Fire',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-fire-extinguisher',
				'name'  => 'Fire Extinguisher',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-flag',
				'name'  => 'Flag',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-flag-checkered',
				'name'  => 'Flag',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-flag-o',
				'name'  => 'Flag',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-flash',
				'name'  => 'Flash',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-flask',
				'name'  => 'Flask',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-folder',
				'name'  => 'Folder',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-folder-open',
				'name'  => 'Folder Open',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-folder-o',
				'name'  => 'Folder',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-folder-open-o',
				'name'  => 'Folder Open',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-futbol-o',
				'name'  => 'Foot Ball',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-frown-o',
				'name'  => 'Frown',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-gamepad',
				'name'  => 'Gamepad',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-gavel',
				'name'  => 'Gavel',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-gear',
				'name'  => 'Gear',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-gears',
				'name'  => 'Gears',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-gift',
				'name'  => 'Gift',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-glass',
				'name'  => 'Glass',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-globe',
				'name'  => 'Globe',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-graduation-cap',
				'name'  => 'Graduation Cap',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-group',
				'name'  => 'Group',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-hand-lizard-o',
				'name'  => 'Hand',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-hand-paper-o',
				'name'  => 'Hand',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-hand-peace-o',
				'name'  => 'Hand',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-hand-pointer-o',
				'name'  => 'Hand',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-hand-rock-o',
				'name'  => 'Hand',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-hand-scissors-o',
				'name'  => 'Hand',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-hand-spock-o',
				'name'  => 'Hand',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-hdd-o',
				'name'  => 'HDD',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-hashtag',
				'name'  => 'Hash Tag',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-headphones',
				'name'  => 'Headphones',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-home',
				'name'  => 'Home',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-hourglass-o',
				'name'  => 'Hourglass',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-hourglass-start',
				'name'  => 'Hourglass',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-hourglass-half',
				'name'  => 'Hourglass',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-hourglass-end',
				'name'  => 'Hourglass',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-hourglass',
				'name'  => 'Hourglass',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-history',
				'name'  => 'History',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-inbox',
				'name'  => 'Inbox',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-industry',
				'name'  => 'Industry',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-info',
				'name'  => 'Info',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-info-circle',
				'name'  => 'Info',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-key',
				'name'  => 'Key',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-keyboard-o',
				'name'  => 'Keyboard',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-language',
				'name'  => 'Language',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-laptop',
				'name'  => 'Laptop',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-leaf',
				'name'  => 'Leaf',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-legal',
				'name'  => 'Legal',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-lemon-o',
				'name'  => 'Lemon',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-level-down',
				'name'  => 'Level Down',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-level-up',
				'name'  => 'Level Up',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-life-ring',
				'name'  => 'Life Buoy',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-lightbulb-o',
				'name'  => 'Lightbulb',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-location-arrow',
				'name'  => 'Location Arrow',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-lock',
				'name'  => 'Lock',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-magic',
				'name'  => 'Magic',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-magnet',
				'name'  => 'Magnet',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-mail-forward',
				'name'  => 'Mail Forward',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-mail-reply',
				'name'  => 'Mail Reply',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-mail-reply-all',
				'name'  => 'Mail Reply All',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-male',
				'name'  => 'Male',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-map',
				'name'  => 'Map',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-map-o',
				'name'  => 'Map',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-map-marker',
				'name'  => 'Map Marker',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-map-pin',
				'name'  => 'Map Pin',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-map-signs',
				'name'  => 'Map Signs',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-meh-o',
				'name'  => 'Meh',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-microphone',
				'name'  => 'Microphone',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-microphone-slash',
				'name'  => 'Microphone',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-minus',
				'name'  => 'Minus',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-minus-circle',
				'name'  => 'Minus',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-mobile',
				'name'  => 'Mobile',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-mobile-phone',
				'name'  => 'Mobile Phone',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-moon-o',
				'name'  => 'Moon',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-mouse-pointer',
				'name'  => 'Mouse Pointer',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-music',
				'name'  => 'Music',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-newspaper-o',
				'name'  => 'Newspaper',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-object-group',
				'name'  => 'Object Group',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-object-ungroup',
				'name'  => 'Object Ungroup',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-paint-brush',
				'name'  => 'Paint Brush',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-paper-plane',
				'name'  => 'Paper Plane',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-paper-plane-o',
				'name'  => 'Paper Plane',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-paw',
				'name'  => 'Paw',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-pencil',
				'name'  => 'Pencil',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-pencil-square',
				'name'  => 'Pencil',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-pencil-square-o',
				'name'  => 'Pencil',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-phone',
				'name'  => 'Phone',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-percent',
				'name'  => 'Percent',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-phone-square',
				'name'  => 'Phone',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-picture-o',
				'name'  => 'Picture',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-plug',
				'name'  => 'Plug',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-plus',
				'name'  => 'Plus',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-plus-circle',
				'name'  => 'Plus',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-power-off',
				'name'  => 'Power Off',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-print',
				'name'  => 'Print',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-puzzle-piece',
				'name'  => 'Puzzle Piece',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-qrcode',
				'name'  => 'QR Code',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-question',
				'name'  => 'Question',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-question-circle',
				'name'  => 'Question',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-question-circle-o',
				'name'  => 'Question',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-quote-left',
				'name'  => 'Quote Left',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-quote-right',
				'name'  => 'Quote Right',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-random',
				'name'  => 'Random',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-rebel',
				'name'  => 'Rebel',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-recycle',
				'name'  => 'Recycle',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-registered',
				'name'  => 'Registered',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-reply',
				'name'  => 'Reply',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-reply-all',
				'name'  => 'Reply All',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-retweet',
				'name'  => 'Retweet',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-road',
				'name'  => 'Road',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-rss',
				'name'  => 'RSS',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-rss-square',
				'name'  => 'RSS Square',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-search',
				'name'  => 'Search',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-search-minus',
				'name'  => 'Search Minus',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-search-plus',
				'name'  => 'Search Plus',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-server',
				'name'  => 'Server',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-share',
				'name'  => 'Share',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-share-alt',
				'name'  => 'Share',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-share-alt-square',
				'name'  => 'Share',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-share-square',
				'name'  => 'Share',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-share-square-o',
				'name'  => 'Share',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-shield',
				'name'  => 'Shield',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-shopping-cart',
				'name'  => 'Shopping Cart',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-shopping-bag',
				'name'  => 'Shopping Bag',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-shopping-basket',
				'name'  => 'Shopping Basket',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sign-in',
				'name'  => 'Sign In',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sign-out',
				'name'  => 'Sign Out',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-signal',
				'name'  => 'Signal',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sitemap',
				'name'  => 'Sitemap',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sliders',
				'name'  => 'Sliders',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-smile-o',
				'name'  => 'Smile',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sort',
				'name'  => 'Sort',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sort-asc',
				'name'  => 'Sort ASC',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sort-desc',
				'name'  => 'Sort DESC',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sort-down',
				'name'  => 'Sort Down',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sort-up',
				'name'  => 'Sort Up',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sort-alpha-asc',
				'name'  => 'Sort Alpha ASC',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sort-alpha-desc',
				'name'  => 'Sort Alpha DESC',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sort-amount-asc',
				'name'  => 'Sort Amount ASC',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sort-amount-desc',
				'name'  => 'Sort Amount DESC',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sort-numeric-asc',
				'name'  => 'Sort Numeric ASC',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sort-numeric-desc',
				'name'  => 'Sort Numeric DESC',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-spoon',
				'name'  => 'Spoon',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-star',
				'name'  => 'Star',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-star-half',
				'name'  => 'Star Half',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-star-half-o',
				'name'  => 'Star Half',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-star-half-empty',
				'name'  => 'Star Half Empty',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-star-half-full',
				'name'  => 'Star Half Full',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-star-o',
				'name'  => 'Star',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sticky-note',
				'name'  => 'Sticky Note',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sticky-note-o',
				'name'  => 'Sticky Note',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-street-view',
				'name'  => 'Street View',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-suitcase',
				'name'  => 'Suitcase',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-sun-o',
				'name'  => 'Sun',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-tablet',
				'name'  => 'Tablet',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-tachometer',
				'name'  => 'Tachometer',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-tag',
				'name'  => 'Tag',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-tags',
				'name'  => 'Tags',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-tasks',
				'name'  => 'Tasks',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-television',
				'name'  => 'Television',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-terminal',
				'name'  => 'Terminal',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-thumb-tack',
				'name'  => 'Thumb Tack',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-thumbs-down',
				'name'  => 'Thumbs Down',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-thumbs-up',
				'name'  => 'Thumbs Up',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-thumbs-o-down',
				'name'  => 'Thumbs Down',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-thumbs-o-up',
				'name'  => 'Thumbs Up',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-ticket',
				'name'  => 'Ticket',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-times',
				'name'  => 'Times',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-times-circle',
				'name'  => 'Times',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-times-circle-o',
				'name'  => 'Times',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-tint',
				'name'  => 'Tint',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-toggle-down',
				'name'  => 'Toggle Down',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-toggle-left',
				'name'  => 'Toggle Left',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-toggle-right',
				'name'  => 'Toggle Right',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-toggle-up',
				'name'  => 'Toggle Up',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-toggle-off',
				'name'  => 'Toggle Off',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-toggle-on',
				'name'  => 'Toggle On',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-trademark',
				'name'  => 'Trademark',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-trash',
				'name'  => 'Trash',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-trash-o',
				'name'  => 'Trash',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-tree',
				'name'  => 'Tree',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-trophy',
				'name'  => 'Trophy',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-tty',
				'name'  => 'TTY',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-umbrella',
				'name'  => 'Umbrella',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-university',
				'name'  => 'University',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-unlock',
				'name'  => 'Unlock',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-unlock-alt',
				'name'  => 'Unlock',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-unsorted',
				'name'  => 'Unsorted',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-upload',
				'name'  => 'Upload',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-user',
				'name'  => 'User',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-users',
				'name'  => 'Users',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-user-plus',
				'name'  => 'User: Add',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-user-times',
				'name'  => 'User: Remove',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-user-secret',
				'name'  => 'User: Password',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-video-camera',
				'name'  => 'Video Camera',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-volume-down',
				'name'  => 'Volume Down',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-volume-off',
				'name'  => 'Volume Of',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-volume-up',
				'name'  => 'Volume Up',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-warning',
				'name'  => 'Warning',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-wifi',
				'name'  => 'WiFi',
			),
			array(
				'group' => 'web-application',
				'id'    => 'fa-wrench',
				'name'  => 'Wrench',
			),
		);

		/**
		 * Filter dashicon icons.
		 *
		 * @param array $icons Icon names.
		 */
		$icons = apply_filters( 'skeleton/iconfonts/fontawesome/icons', $icons );

		return $icons;
	}
}
