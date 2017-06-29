<?php
namespace Skeleton\Support;

use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Filesystem {
	/**
	 * Scan recursive a directory.
	 *
	 * @param  string $directory A real directory location.
	 * @param  flags  $flags     RecursiveDirectoryIterator flags searching.
	 * @return RecursiveIteratorIterator
	 */
	public static function scandir( $directory, $flags = FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS ) {
		$iterator = new RecursiveDirectoryIterator( $directory, $flags );
		return new RecursiveIteratorIterator( $iterator, RecursiveIteratorIterator::SELF_FIRST );
	}

	/**
	 * Unzip a file and return correctly directory.
	 *
	 * @param  string $zipfile     Zip file path.
	 * @param  string $destination The base directory where extract to.
	 * @return WP_Error|array
	 */
	public static function unzip( $zipfile, $destination ) {
		$filesystem = static::wp_filesystem();

		// We need a working directory first.
		$working_directory = trailingslashit( $destination . '/' . pathinfo( $zipfile, PATHINFO_FILENAME ) );
		$unzip_result = unzip_file( $zipfile, $working_directory );

		if ( is_wp_error( $unzip_result ) ) {
			return $unzip_result; // WP_Error instance.
		}

		// Find the right folder.
		$source_files = array_keys( $filesystem->dirlist( $working_directory ) );

		if ( count( $source_files ) === 0 ) {
			return new WP_Error( 'incompatible_archive', esc_html__( 'Incompatible archive', 'skeleton' ) );
		} elseif ( 1 == count( $source_files ) && $filesystem->is_dir( $working_directory . $source_files[0] ) ) {
			$directory = $working_directory . trailingslashit( $source_files[0] );
		} else {
			$directory = $working_directory;
		}

		return array( $directory, $working_directory );
	}

	/**
	 * Initialized (if need) and return the Wordpress filesystem.
	 *
	 * @return WP_Filesystem_Base
	 */
	public static function wp_filesystem() {
		global $wp_filesystem;

		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		return $wp_filesystem;
	}
}
