<?php
namespace Skeleton\Iconfonts;

use WP_Error;
use WP_Filesystem_Base;
use Skeleton\WP_Option;
use Skeleton\Skeleton;
use Skeleton\Support\Filesystem;

class Installer {
	/**
	 * Skeleton container instance.
	 *
	 * @var Skeleton
	 */
	protected $skeleton;

	/**
	 * Fonticons Manager instance.
	 *
	 * @var Manager
	 */
	protected $manager;

	/**
	 * WP_Option instance.
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * Collection of extractors.
	 *
	 * @var array
	 */
	protected $extractors;

	/**
	 * Constructor installer.
	 *
	 * @param Skeleton  $skeleton
	 * @param Manager   $manager
	 * @param WP_Option $options
	 */
	public function __construct( Skeleton $skeleton, Manager $manager, WP_Option $options ) {
		$this->skeleton = $skeleton;
		$this->manager  = $manager;
		$this->options  = $options;

		$this->extractors = apply_filters( 'skeleton/iconfonts/extractors', array(
			'Awethemes\Skeleton\Iconfonts\Extractor\Pixeden_Extractor',
			'Awethemes\Skeleton\Iconfonts\Extractor\Elusive_Extractor',
			'Awethemes\Skeleton\Iconfonts\Extractor\Themify_Extractor',
			'Awethemes\Skeleton\Iconfonts\Extractor\Fontello_Extractor',
			'Awethemes\Skeleton\Iconfonts\Extractor\Ionicons_Extractor',
			'Awethemes\Skeleton\Iconfonts\Extractor\Octicons_Extractor',
			'Awethemes\Skeleton\Iconfonts\Extractor\Foundation_Extractor',
			'Awethemes\Skeleton\Iconfonts\Extractor\Icomoon_App_Extractor',
			'Awethemes\Skeleton\Iconfonts\Extractor\Fontawesome_Extractor',
			'Awethemes\Skeleton\Iconfonts\Extractor\Paymentfont_Extractor',
		));
	}

	/**
	 * Install icon pack form a directory.
	 *
	 * @param  string  $directory         Source directory.
	 * @param  string  $working_directory The directory we working on it.
	 * @param  boolean $delete_after      Delete directory or working_directory after that.
	 * @return Icon_Pack|WP_Error
	 */
	public function install( $directory, $working_directory = null, $delete_after = false ) {
		$extractor = $this->guest_extractor( $directory );

		if ( ! $extractor ) {
			$this->delete_directory( $delete_after, $directory, $working_directory );
			return new WP_Error( 'not_support', esc_html__( 'Not support', 'skeleton' ) );
		}

		// Extract icon pack data.
		$result = $extractor->extract();

		if ( is_wp_error( $result ) ) {
			$this->delete_directory( $delete_after, $directory, $working_directory );
			return $result; // WP_Error instance.
		}

		$result->dest( $this->skeleton['iconfonts_upload_icons_dir'] );
		$this->options->set( $result->id, true );

		$this->delete_directory( $delete_after, $directory, $working_directory );
	}

	/**
	 * Install icon form zip file format.
	 *
	 * @param  string $zipfile Zipfile path.
	 * @return Icon_Pack|WP_Error
	 */
	public function zip_install( $zipfile ) {
		$unzip_result = Filesystem::unzip( $zipfile, $this->skeleton['iconfonts_upload_tmp_dir'] );

		if ( is_wp_error( $unzip_result ) ) {
			return $unzip_result;
		}

		list( $directory, $working_directory ) = $unzip_result;

		return $this->install( $directory, $working_directory, true );
	}

	/**
	 * Guest extractor by directory.
	 *
	 * @param  string $directory Icon font directory.
	 * @return Extractor_Interface|null
	 */
	public function guest_extractor( $directory ) {
		foreach ( $this->extractors as $class ) {
			if ( ! class_exists( $class ) ) {
				continue;
			}

			$extractor = new $class( $directory );
			if ( ! $extractor instanceof Extractor\Extractor_Interface ) {
				continue;
			}

			if ( $extractor->check() ) {
				return $extractor;
			}
		}
	}

	/**
	 * Utils: Delete directory and working directory.
	 *
	 * @param  bool   $check            Check delete directory.
	 * @param  string $directory         Source directory.
	 * @param  string $working_directory The directory we working on it.
	 */
	protected function delete_directory( $check, $directory, $working_directory = null ) {
		if ( ! $check ) {
			return;
		}

		$filesystem = Filesystem::wp_filesystem();

		if ( $filesystem->is_dir( $directory ) ) {
			$filesystem->rmdir( $directory, true );
		}

		if ( $working_directory && $filesystem->is_dir( $working_directory ) ) {
			$filesystem->rmdir( $working_directory, true );
		}
	}
}
