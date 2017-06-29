<?php
namespace Skeleton\Iconfonts\Extractor;

use Skeleton\Support\Multidimensional;

/**
 * Icomoon-App extractor provider.
 *
 * @link https://icomoon.io/app
 */
class Icomoon_App_Extractor extends Extractor_Abstract implements Rewritable {
	/**
	 * The metadata file name.
	 *
	 * @var string
	 */
	protected $metadata = 'selection.json';

	/**
	 * The stylesheet file name.
	 *
	 * @var string
	 */
	protected $stylesheet = 'style.css';

	/**
	 * List of files, directories match with icon pack.
	 *
	 * @var array
	 */
	protected $directory_structure = array( 'demo-files/', 'selection.json', 'style.css', 'demo.html' );

	/**
	 * Doing extract icon pack data.
	 *
	 * @param  Extractor_Result $result Extractor icon pack instance.
	 * @return void
	 */
	protected function doing_extract( Extractor_Result $result ) {
		$json = $result->get_metadata_contents();

		// Checking valid metadata.
		if ( empty( $json['icons'] ) || empty( $json['preferences']['fontPref'] ) ) {
			return;
		}

		$fontpref = $json['preferences']['fontPref'];

		// Extract data.
		$result->name = Multidimensional::find( $json, 'metadata.name',
			Multidimensional::find( $fontpref, 'metadata.fontFamily', '' )
		);

		$result->version = sprintf( '%s.%s',
			Multidimensional::find( $fontpref, 'metadata.majorVersion', '1' ),
			Multidimensional::find( $fontpref, 'metadata.minorVersion', '0' )
		);

		$result->id = sanitize_key( $result->name );

		$this->doing_extract_icons( $json, $result );
	}

	/**
	 * Doing extract icons from metatata.
	 *
	 * @param  array            $json
	 * @param  Extractor_Result $result
	 */
	protected function doing_extract_icons( $json, Extractor_Result $result ) {
		$fontconfig = $json['preferences']['fontPref'];

		foreach ( $json['icons'] as $raw_icon ) {
			if ( ! isset( $raw_icon['properties']['name'] ) ) {
				continue;
			}

			$postfix = isset( $fontconfig['postfix'] ) ? $fontconfig['postfix'] : '';
			$prefix = $fontconfig['prefix'];

			if ( $result->id && 0 === strpos( $prefix, 'icon' ) ) {
				$prefix = $result->id . '-';
			}

			$icon_name = $raw_icon['properties']['name'];
			$icon_class = $prefix . $icon_name . $postfix;

			if ( isset( $fontconfig['selector'] ) && 'class' === $fontconfig['selector'] && $fontconfig['classSelector'] ) {
				$selector = str_replace( '.', '', $fontconfig['classSelector'] );

				if ( $result->id && 0 === strpos( $selector, 'icon' ) ) {
					$selector = $result->id;
				}

				$icon_class = $selector . ' ' . $icon_class;
			}

			$result->add_icon( $icon_class, $icon_name );
		}
	}
}
