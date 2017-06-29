<?php
namespace Skeleton\Iconfonts\Extractor;

interface Extractor_Interface {
	/**
	 * Check directory structure match with this iconpack.
	 *
	 * @return boolean
	 */
	public function check();

	/**
	 * Extract icon pack infomation from directory.
	 *
	 * @return Extractor_Result|WP_Error
	 */
	public function extract();
}
