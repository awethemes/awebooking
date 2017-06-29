<?php
namespace Skeleton\Iconfonts\Extractor;

/**
 * Elusive extractor provider.
 *
 * @link http://elusiveicons.com
 */
class Elusive_Extractor extends Fontawesome_Extractor {
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
	protected $stylesheet = 'css/elusive-icons.css';

	/**
	 * List of files, directories match with iconpack.
	 *
	 * @var array
	 */
	protected $directory_structure = array( 'fonts/', 'scss/', 'css/elusive-icons.css' );

	/**
	 * Icon prefix name.
	 *
	 * @var string
	 */
	protected $prefix = 'el-';

	/**
	 * Src icon prefix name.
	 *
	 * @var string
	 */
	protected $src_prefix = '.#{$el-css-prefix}';

	/**
	 * Doing extract icon pack data.
	 *
	 * @param  Extractor_Result $result Extractor icon pack instance.
	 * @return void
	 */
	protected function doing_extract( Extractor_Result $result ) {
		$result->id = 'el';
		$result->name = esc_html__( 'Elusive', 'skeleton' );

		$this->doing_extract_icons( $result );

		// Extract version.
		$variable_contents = file_get_contents( $this->directory . 'scss/_variables.scss' );
		if ( preg_match( '/\$el-version:\s+"([0-9.]+)"/', $variable_contents, $matches ) ) {
			$result->version = trim( $matches[1] );
		}
	}
}
