<?php
require_once __DIR__ . '/../vendor/awethemes/skeleton/composer-cleaner.php';

class AweBooking_Composer_Cleaner extends Composer_Cleaner {
	/**
	 * Rule patterns for packages.
	 *
	 * @return array
	 */
	protected static function get_rules() {
		$docs  = 'README* CHANGELOG* FAQ* CONTRIBUTING* HISTORY* UPGRADING* UPGRADE* package* demo example examples doc docs readme* .github* .editorconfig .gitignore';
		$tests = '.travis.yml .scrutinizer.yml phpunit.xml* phpunit.php test tests Tests travis';

		$rules = parent::get_rules();
		$rules['psr/log']         = [ $docs, $tests ];
		$rules['roomify/bat']     = [ $docs, $tests ];
		$rules['nesbot/carbon']   = [ $docs, $tests ];
		$rules['monolog/monolog'] = [ $docs, $tests, '.php_cs' ];
		$rules['league/period']   = [ $docs, $tests, 'humbug.json' ];
		$rules['pelago/emogrifier'] = [ $docs, $tests, 'Configuration', 'CODE_OF_CONDUCT.md' ];
		$rules['tightenco/collect'] = [ $docs, $tests, 'collect-logo.png', 'composer.lock' ];
		$rules['ericmann/wp-session-manager'] = [ $docs, $tests ];

		return $rules;
	}
}
