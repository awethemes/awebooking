<?php
namespace Skeleton\Support;

use Composer\Composer;
use Composer\Script\Event;
use Composer\Util\Filesystem;
use Composer\Package\BasePackage;

class Composer_Scripts {
	/**
	 * Handle the post-install Composer event.
	 *
	 * @param  \Composer\Script\Event $event Composer Event Instance.
	 */
	public static function clean( Event $event ) {
		$composer = $event->getComposer();

		$vendor_dirs = array(
			$composer->getConfig()->get( 'vendor-dir' ),
			dirname( $composer->getConfig()->get( 'vendor-dir' ) ) . '/libs',
		);

		// Clean packages rules.
		$rules = static::get_rules();

		foreach ( $vendor_dirs as $vendor_dir ) {
			if ( ! is_dir( $vendor_dir ) ) {
				continue;
			}

			foreach ( $composer->getRepositoryManager()->getLocalRepository()->getPackages() as $package ) {
				if ( ! $package instanceof BasePackage ) {
					continue;
				}

				$name = $package->getPrettyName();
				$explode_name = explode( '/', $name );
				if ( ! array_key_exists( $name, $rules ) ) {
					continue;
				}

				if ( is_dir( $path = $vendor_dir . '/' . $name ) ) {
					static::clean_package( $path, $rules[ $name ] );
				} elseif ( is_dir( $path = $vendor_dir . '/' . $explode_name[1] ) ) {
					static::clean_package( $path, $rules[ $name ] );
				}
			}
		}
	}

	/**
	 * Clean a package path.
	 *
	 * @param  string $package_path Package path.
	 * @param  array  $rules        Package clean rules.
	 */
	protected static function clean_package( $package_path, $rules ) {
		static $filesystem;

		if ( ! $filesystem ) {
			$filesystem = new Filesystem();
		}

		$rules = implode( ' ', $rules );
		$patterns = array_map( 'trim', explode( ' ', trim( $rules ) ) );

		foreach ( $patterns as $pattern ) {
			try {
				foreach ( glob( $package_path . '/' . $pattern ) as $path ) {
					$filesystem->remove( $path );
				}
			} catch ( \Exception $e ) {
				continue;
			}
		}
	}

	/**
	 * Rule patterns for packages.
	 *
	 * @return array
	 */
	protected static function get_rules() {
		$docs = 'README* CHANGELOG* FAQ* CONTRIBUTING* HISTORY* UPGRADING* UPGRADE* package* demo example examples doc docs readme* .github*';
		$tests = '.travis.yml .scrutinizer.yml phpunit.xml* phpunit.php test tests Tests travis';

		return array(
			'pimple/pimple'         => array( $docs, $tests, 'ext*', 'src/Pimple/Tests*' ),
			'erusev/parsedown'      => array( $docs, $tests ),
			'vlucas/valitron'       => array( $docs, $tests, 'lang' ),
			'jtsternberg/twitterwp' => array( $docs, $tests, '.git*' ),
			'webdevstudios/cmb2'    => array( $docs, $tests, 'languages/*.mo languages/*.po' ),
			'webdevstudios/cmb2-post-search-field' => array( $docs, $tests, '*.png' ),
			'webdevstudios/taxonomy_single_term'   => array( $docs, $tests ),

			'roomify/bat'       => array( $docs, $tests ),
			'nesbot/carbon'     => array( $docs, $tests ),
			'pelago/emogrifier' => array( $docs, $tests, 'Configuration', 'CODE_OF_CONDUCT.md' ),
		);
	}
}
