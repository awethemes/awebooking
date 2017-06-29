<?php

require_once trailingslashit( __DIR__ ) . 'inc/helpers.php';

if ( ! class_exists( 'Skeleton\\Support\\Autoload', false ) ) {
	require_once trailingslashit( __DIR__ ) . 'inc/Support/Autoload.php';
}

// Autoloader.
skeleton_psr4_autoloader(array(
	'Pimple\\'   => trailingslashit( __DIR__ ) . 'libs/pimple/src/Pimple/',
	'Valitron\\' => trailingslashit( __DIR__ ) . 'libs/valitron/src/Valitron/',
	'Skeleton\\' => trailingslashit( __DIR__ ) . 'inc/',
));
