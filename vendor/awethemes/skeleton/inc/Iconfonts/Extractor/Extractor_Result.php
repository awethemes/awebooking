<?php
namespace Skeleton\Iconfonts\Extractor;

use Skeleton\Support\Filesystem;

class Extractor_Result {
	/**
	 * Iconpack unique ID.
	 *
	 * @var string
	 */
	public $id = '';

	/**
	 * Iconpack display name.
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Iconpack version.
	 *
	 * @var string
	 */
	public $version = '';

	/**
	 * Iconpack icons.
	 *
	 * @var string
	 */
	public $icons = array();

	/**
	 * Iconpack groups.
	 *
	 * @var string
	 */
	public $groups = array();

	/**
	 * An array store icon-font paths.
	 *
	 * @var string
	 */
	public $font_paths = array();

	/**
	 * The metadata file path.
	 *
	 * @var string
	 */
	public $metadata_path = '';

	/**
	 * The stylesheet file path.
	 *
	 * @var string
	 */
	public $stylesheet_path = '';

	/**
	 * The Extractor instance.
	 *
	 * @var Extractor_Interface
	 */
	protected $extractor;

	/**
	 * The Wordpress filesystem instance.
	 *
	 * @var WP_Filesystem_Base
	 */
	protected $filesystem;

	/**
	 * Supplied $args override class property defaults.
	 *
	 * @param Extractor_Interface $extractor The extractor instance.
	 * @param array               $args      Optional. Arguments to override class property defaults.
	 */
	public function __construct( Extractor_Interface $extractor, array $args = array() ) {
		$keys = array_keys( get_object_vars( $this ) );

		foreach ( $keys as $key ) {
			if ( isset( $args[ $key ] ) ) {
				$this->{$key} = $args[ $key ];
			}
		}

		$this->extractor  = $extractor;
		$this->filesystem = Filesystem::wp_filesystem();
	}

	/**
	 * ..
	 *
	 * @param  [type] $dest
	 * @return [type]       [description]
	 */
	public function dest( $base ) {
		$destination = trailingslashit( $base ) . $this->id . '/';

		// Ensure destination exits.
		if ( ! is_dir( $destination ) ) {
			@mkdir( $destination, 0755, true );
		}

		if ( ! is_dir( $destination . 'fonts/' ) ) {
			@mkdir( $destination . 'fonts/', 0755, true );
		}

		// Move fonts to new folder.
		foreach ( $this->font_paths as $path ) {
			if ( $this->extractor instanceof Rewritable ) {
				copy( $path, $destination . 'fonts/' . $this->id . '.' . pathinfo( $path, PATHINFO_EXTENSION ) );
			} else {
				copy( $path, $destination . 'fonts/' . basename( $path ) );
			}
		}

		// Write style.css, icons.json.
		file_put_contents( $destination . 'style.css', $this->get_rewrite_stylesheet() );
		file_put_contents( $destination . 'metadata.json', json_encode( $this->to_array() ) . "\n" );
		file_put_contents( $destination . 'index.php', "<?php\n// Silence is golden.\n" );
	}

	/**
	 * Return metadata data contents.
	 *
	 * If see a json file, decode json as array and return it.
	 *
	 * @return string
	 */
	public function get_metadata_contents() {
		$contents = file_get_contents( $this->metadata_path );

		if ( 'json' === pathinfo( $this->metadata_path, PATHINFO_EXTENSION ) ) {
			$contents = json_decode( $contents, true );
		}

		return $contents;
	}

	/**
	 * Return stylesheet data contents.
	 *
	 * @return string
	 */
	public function get_stylesheet_contents() {
		return file_get_contents( $this->stylesheet_path );
	}

	/**
	 * Utils: Rewrite and minify CSS.
	 *
	 * @return string
	 */
	protected function get_rewrite_stylesheet() {
		$stylesheet = $this->get_stylesheet_contents();

		// Change fonts paths.
		$stylesheet = preg_replace( '/url\(([\'"])?/', 'url($1::', $stylesheet ); // ...
		$stylesheet = preg_replace( '/\.\.?\/|fonts?\//', '::', $stylesheet ); // Replace ..|fonts with temp string ::.
		$stylesheet = preg_replace( '/[\:\:]{2,}+/', 'fonts/', $stylesheet ); // Replace `::` temp string with 'fonts/'.

		if ( $this->extractor instanceof Rewritable ) {
			// $stylesheet = Utils::minify_css( $stylesheet );

			// Change fonts name.
			foreach ( $this->font_paths as $path ) {
				$newname = $this->id . '.' . pathinfo( $path, PATHINFO_EXTENSION );
				$stylesheet = str_replace( basename( $path ), $newname, $stylesheet );
			}

			// Never use prefix .icon for icon name.
			$stylesheet = str_replace( 'icon-', $this->id . '-', $stylesheet );
			$stylesheet = str_replace( array( '.icon {', 'i {' ), '[class^="' . $this->id . '-"], [class*=" ' . $this->id . '-"] {', $stylesheet );
		}

		// Return parser CSS with blank line at end.
		return trim( $stylesheet ) . "\n";
	}

	public function add_icon( $id, $name, $group = '' ) {
		if ( empty( $id ) ) {
			return;
		}

		// Format icon name.
		$name = ucfirst( str_replace( array( '-', ' o' ), array( ' ', '' ), $name ) );

		$this->icons[] = array(
			'id'    => $id,
			'name'  => $name,
			'group' => $group,
		);
	}

	/**
	 * Get an array of this result.
	 *
	 * @return array
	 */
	public function to_array() {
		return array(
			'id'      => $this->id,
			'name'    => $this->name,
			'version' => $this->version,
			'icons'   => $this->icons,
			'groups'  => $this->groups,
		);
	}
}
