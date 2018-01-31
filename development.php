<?php

$packages = [
	__DIR__ . '/awethemes/skeleton/skeleton.php',
	__DIR__ . '/awethemes/skeleton/vendor/autoload.php',
	__DIR__ . '/awethemes/container/vendor/autoload.php',
	__DIR__ . '/awethemes/wp-http/vendor/autoload.php',
	__DIR__ . '/awethemes/wp-object/vendor/autoload.php',
	__DIR__ . '/awethemes/wp-session/vendor/autoload.php',
];

foreach ( $packages as $path ) {
	require $path;
}
