<?php
namespace Skeleton\Iconfonts\Extractor;

/**
 * FontAwesome extractor provider.
 *
 * @link http://fontawesome.io
 */
class Fontawesome_Extractor extends Extractor_Abstract {
	/**
	 * The metadata file name.
	 *
	 * @var string
	 */
	protected $metadata = 'scss/_icons.scss';

	/**
	 * The stylesheet file name.
	 *
	 * @var string
	 */
	protected $stylesheet = 'css/font-awesome.css';

	/**
	 * List of files, directories match with iconpack.
	 *
	 * @var array
	 */
	protected $directory_structure = array( 'fonts/', 'scss/', 'css/font-awesome.css' );

	/**
	 * Icon prefix name.
	 *
	 * @var string
	 */
	protected $prefix = 'fa-';

	/**
	 * Src icon prefix name.
	 *
	 * @var string
	 */
	protected $src_prefix = '.#{$fa-css-prefix}';

	/**
	 * Doing extract icon pack data.
	 *
	 * @param  Extractor_Result $result Extractor icon pack instance.
	 * @return void
	 */
	protected function doing_extract( Extractor_Result $result ) {
		$result->id = 'fa';
		$result->name = esc_html__( 'Font Awesome', 'skeleton' );

		$this->doing_extract_icons( $result );

		// Extract version.
		$variable_contents = file_get_contents( $this->directory . 'scss/_variables.scss' );
		if ( preg_match( '/\$fa-version:\s+"([0-9.]+)"/', $variable_contents, $matches ) ) {
			$result->version = trim( $matches[1] );
		}
	}

	/**
	 * Doing extract icons from metatata.
	 *
	 * @param  Extractor_Result $result
	 */
	protected function doing_extract_icons( Extractor_Result $result ) {
		$handle = @fopen( $result->metadata_path, 'r' );

		while ( ! feof( $handle ) ) {
			$line = trim( fgets( $handle, 4096 ) );

			if ( false === strpos( $line, $this->src_prefix ) ) {
				continue;
			}

			if ( preg_match( '/^' . preg_quote( $this->src_prefix, '/' ) . '-([a-z0-9-]+)\:before/', $line, $matches ) ) {
				$result->add_icon( $this->prefix . $matches[1], $matches[1] );
			}
		}
	}
}
