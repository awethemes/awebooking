<?php
namespace Skeleton\Iconfonts\Icons;

class Iconpack_Plugable extends Iconpack {

	public static function import( $id ) {
		$metadata = file_get_contents( skeleton( 'iconfonts_upload_icons_dir' ) . $id . '/metadata.json' );

		$args = json_decode( $metadata, true );

		return new static( $args );
	}

	public function register_styles() {
		wp_register_style( $this->id, skeleton( 'iconfonts_upload_icons_url' ) . $this->id . '/style.css' );
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->id );
	}
}
