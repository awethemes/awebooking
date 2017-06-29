<?php
namespace Skeleton\Iconfonts\Extractor;

use WP_Error;
use Skeleton\Support\Filesystem;

abstract class Extractor_Abstract implements Extractor_Interface {
	/**
	 * Root directory.
	 *
	 * @var string
	 */
	protected $directory;

	/**
	 * The relative path of metadata file.
	 *
	 * @var string
	 */
	protected $metadata = 'metadata.json';

	/**
	 * The relative path of stylesheet file.
	 *
	 * @var string
	 */
	protected $stylesheet = 'style.css';

	/**
	 * The relative path of fonts directory.
	 *
	 * @var string
	 */
	protected $fonts_directory = 'fonts';

	/**
	 * Webbfont extensions.
	 *
	 * @var array
	 */
	protected $webfont_extensions = array( 'svg', 'otf', 'eot', 'ttf', 'woff', 'woff2' );

	/**
	 * List of files, directories match with icon pack.
	 *
	 * Directory must be end with "/", ex: array( 'fonts/', 'style.css' )
	 *
	 * @var array
	 */
	protected $directory_structure = array();

	/**
	 * Constructor of class.
	 *
	 * @param string $directory Font icon directory.
	 */
	public function __construct( $directory ) {
		$this->directory = trailingslashit( wp_normalize_path( $directory ) );
	}

	/**
	 * Get root directory.
	 *
	 * @return string
	 */
	public function get_directory() {
		return $this->directory;
	}

	/**
	 * Check directory structure match with this icon pack.
	 *
	 * @return boolean
	 */
	public function check() {
		$match = false;

		foreach ( $this->directory_structure as $name ) {
			if ( substr( $name, -1 ) == '/' ) {
				$match = is_dir( $this->directory . $name );
			} else {
				$match = is_file( $this->directory . $name );
			}

			// If we see any "false" result, break the loop and return false.
			if ( false === $match ) {
				break;
			}
		}

		return $match;
	}

	/**
	 * Extract icon pack data from directory.
	 *
	 * @return WP_Error|Extractor_Result
	 */
	public function extract() {
		$args = array();
		$this->before_extract( $args );

		// Checking valid args.
		if ( empty( $args['metadata_path'] ) || empty( $args['stylesheet_path'] ) || empty( $args['font_paths'] ) ) {
			return new WP_Error( 'invalid_directory_structure', esc_html__( 'Invalid Directory Structure.', 'skeleton' ) );
		}

		if ( filesize( $args['metadata_path'] ) > 2097152 ) { // 2097152 = 2MB
			return new WP_Error( 'metadata_too_large', esc_html__( 'The metadata file too large, maximum is less than 2MB', 'skeleton' ) );
		}

		// Make a new Extractor_Result.
		$result = new Extractor_Result( $this, $args );

		// Extract missing icons, groups, name, etc...
		$this->doing_extract( $result );

		// Checking valid result.
		if ( empty( $result->id ) || empty( $result->icons ) ) {
			return new WP_Error( 'invalid_iconpack', esc_html__( 'Invalid icon pack data.', 'skeleton' ) );
		}

		return $result;
	}

	/**
	 * Before extractor, return an array metadata, stylesheet, fonts path.
	 *
	 * @param  array $args Reference $args variable.
	 * @return void
	 */
	protected function before_extract( &$args ) {
		$args = array();

		if ( is_file( $this->directory . $this->metadata ) ) {
			$args['metadata_path'] = $this->directory . $this->metadata;
		}

		if ( is_file( $this->directory . $this->stylesheet ) ) {
			$args['stylesheet_path'] = $this->directory . $this->stylesheet;
		}

		// Scan fonts directory and get font paths.
		foreach ( Filesystem::scandir( $this->directory . $this->fonts_directory ) as $fontpath => $fileinfo ) {
			$extension = $fileinfo->getExtension();

			if ( in_array( $extension, $this->webfont_extensions ) ) {
				$args['font_paths'][ $extension ] = $fontpath;
			}
		}
	}

	/**
	 * Doing extract icon pack data.
	 *
	 * @param  Extractor_Result $iconpack Extractor icon pack instance.
	 * @return void
	 */
	abstract protected function doing_extract( Extractor_Result $iconpack );
}
