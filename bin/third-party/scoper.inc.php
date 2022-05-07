<?php
/**
 * PHP-Scoper configuration file.
 *
 * @see https://github.com/humbug/php-scoper#configuration
 *
 * @noinspection PhpUndefinedClassInspection
 * @noinspection PhpUndefinedNamespaceInspection
 */

use Isolated\Symfony\Component\Finder\Finder;

$polyfillsBootstraps = array_map(
    static function (SplFileInfo $fileInfo) {
        return $fileInfo->getPathname();
    },
    iterator_to_array(
        Finder::create()
            ->files()
            ->in(__DIR__ . '/vendor/symfony/polyfill-*')
            ->name('bootstrap*.php'),
        false
    )
);

$polyfillsStubs = array_map(
    static function (SplFileInfo $fileInfo) {
        return $fileInfo->getPathname();
    },
    iterator_to_array(
        Finder::create()
            ->files()
            ->in(__DIR__ . '/vendor/symfony/polyfill-*/Resources/stubs')
            ->name('*.php'),
        false
    )
);

return [
    'prefix' => 'AweBooking\\Vendor',

    // See: https://github.com/humbug/php-scoper#finders-and-paths
    'finders' => [
        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->name(['*.php', 'LICENSE', 'composer.json'])
            ->exclude(['tests'])
            ->in(['vendor/psr']),

        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->name(['*.php', 'LICENSE', 'composer.json'])
            ->exclude(['tests', 'lang'])
            ->in(['vendor/vlucas/valitron']),

        // Symfony
        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->name(['*.php', 'LICENSE', 'composer.json'])
            ->exclude(['Test', 'docs'])
            ->in(
                [
                    'vendor/symfony/deprecation-contracts',
                    'vendor/symfony/event-dispatcher-contracts',
                ]
            ),

        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->name(['*.php', 'LICENSE', 'composer.json'])
            ->exclude(['Test', 'docs'])
            ->in(['vendor/symfony/http-foundation']),

        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->name(['*.php', 'LICENSE', 'composer.json'])
            ->exclude(['Test', 'docs'])
            ->in(['vendor/symfony/notifier']),

        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->name(['*.php', 'LICENSE', 'composer.json'])
            ->exclude(['tests', 'docs'])
            ->in(['vendor/doctrine/inflector']),

        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->name(['*.php', 'LICENSE', 'composer.json'])
            ->exclude(['tests'])
            ->notName(['carbon_compat.php'])
            ->in('vendor/cakephp/chronos'),

        // Illuminate
        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->name(['*.php', 'LICENSE', 'composer.json'])
            ->in('vendor/illuminate/container'),

        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->name(['*.php', 'LICENSE', 'composer.json'])
            ->path(
                [
                    'Support/',
                    'Container/',
                    'Pagination/',
                    'Database/',
                    'Events/',
                    'Queue/',
                    'Routing/',
                    'Broadcasting/',
                ]
            )
            ->in('vendor/illuminate/contracts'),

        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->name(['*.php', 'LICENSE', 'composer.json'])
            ->exclude(
                [
                    'Capsule/',
                    'Connectors/',
                    'Console/',
                    'DBAL/',
                    'PDO/',
                    'Eloquent/Factories/',
                ]
            )
            ->notName(
                [
                    'DatabaseServiceProvider.php',
                    'MigrationServiceProvider.php',

                    'PostgresConnection.php',
                    'SQLiteConnection.php',
                    'SqlServerConnection.php',

                    'PostgresProcessor.php',
                    'SQLiteProcessor.php',
                    'SqlServerProcessor.php',

                    'PostgresGrammar.php',
                    'SQLiteGrammar.php',
                    'SqlServerGrammar.php',
                    'MySqlSchemaState.php',
                    'PostgresSchemaState.php',
                    'SqliteSchemaState.php',
                    'SchemaState.php',

                    'PostgresBuilder.php',
                    'SQLiteBuilder.php',
                    'SqlServerBuilder.php',

                    'Seeder.php',
                    'DatabaseManager.php',
                    'ConnectionResolver.php',
                    'ConfigurationUrlParser.php',
                    'MySqlConnection.php',
                ]
            )
            ->in(['vendor/illuminate/database']),

        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->name(['*.php', 'LICENSE', 'composer.json'])
            ->notName(
                [
                    'CallQueuedListener.php',
                    'EventServiceProvider.php',
                    'InvokeQueuedClosure.php',
                    'QueuedClosure.php',
                    'functions.php'
                ]
            )
            ->in('vendor/illuminate/events'),

        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->name(['*.php', 'LICENSE', 'composer.json'])
            ->notName(
                [
                    'CursorPaginationException.php',
                    'PaginationServiceProvider.php',
                    'PaginationState.php',
                ]
            )
            ->exclude(['resources/'])
            ->in('vendor/illuminate/pagination'),

        Finder::create()
            ->files()
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->name(['*.php', 'LICENSE', 'composer.json'])
            ->notName(
                [
                    'AggregateServiceProvider.php',
                    'Carbon.php',
                    'Composer.php',
                    'ConfigurationUrlParser.php',
                    'DateFactory.php',
                    'Env.php',
                    'InteractsWithTime.php',
                    'Manager.php',
                    'MultipleInstanceManager.php',
                    'ServiceProvider.php',
                    'NamespacedItemResolver.php',
                    'ProcessUtils.php',

                    'CapsuleManagerTrait.php',
                    'Localizable.php',
                ]
            )
            ->exclude(['Facades', 'Testing'])
            ->in('vendor/illuminate/support'),

        // roomify/bat
        Finder::create()
	        ->files()
	        ->ignoreVCS(true)
	        ->ignoreDotFiles(true)
	        ->name(['*.php', 'LICENSE', 'composer.json'])
	        ->exclude(['tests'])
	        ->notName(
		        [
			        'SqlLiteDBStore.php',
			        'DrupalDBStore.php',
		        ]
	        )
	        ->in(['vendor/roomify/bat']),

        // Main composer.json file so that we can build a classmap.
        Finder::create()
            ->append(['composer.json']),
    ],

    // See: https://github.com/humbug/php-scoper#patchers
    'patchers' => [
        function (string $file_path, string $prefix, string $contents): string {
            $replaces = [
                '/illuminate/' => 'Illuminate\\\\',
                '/cakephp/chronos/' => 'Cake\\\\Chronos\\\\',
            ];

            foreach ($replaces as $path => $namespace) {
                if (false === strpos($file_path, $path)) {
                    continue;
                }

                $contents = preg_replace(
                    '/' . sprintf('@(var|param|return|throws|mixin|method|see)\s+\\\\%s', $namespace) . '/',
                    sprintf('@$1 \\\\%s\\\\%s', $prefix, $namespace),
                    $contents
                );

                $contents = preg_replace(
                    '/' . sprintf('\|\\\\%s', $namespace) . '/',
                    sprintf('|\\\\%s\\\\%s', $prefix, $namespace),
                    $contents
                );
            }

            return $contents;
        },

        function (string $file_path, string $prefix, string $contents): string {
            if (false === strpos($file_path, '/illuminate/') ||
                false !== strpos($file_path, 'helpers.php')) {
                return $contents;
            }

            $functions = [
                'collect',
                'class_basename',
                'class_uses_recursive',
                'data_get',
                'value',
                'head',
                'last',
                'tap',
            ];

            foreach ($functions as $function) {
                $patterns = [
                    '\(', // tap(collect())
                    '\s?=\s?', // = collect()
                    'return\s', // return collect()
                    '\,\s?', // dispatch($name, collect())
                    '\s\?\?\s', // $condition ?? collect()
                    '\s\?\s', // $condition ? collect() : null
                    '\s\:\s', // $condition ? collect() : null
                    '\.\s?', // $name = $name . class_basename().
                    '\|\|\s?', // if ($isTrue || value($data))
                ];

                $contents = preg_replace(
                    '/' . sprintf('(%s)%s\(', implode('|', $patterns), $function) . '/',
                    sprintf('$1\\\\%s\\\\%s(', $prefix, $function),
                    $contents
                );
            }

            return $contents;
        },
    ],

    // See: https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#excluded-symbols
    'exclude-namespaces' => [
        'Psr',
        'voku\helper',
        'Ramsey\Uuid',
        'Doctrine\Inflector',
        'Symfony\Polyfill',
        'Symfony\Component\VarDumper',
    ],

    'exclude-constants' => [
        // Symfony global constants
        '/^SYMFONY\_[\p{L}_]+$/',
    ],

    'exclude-files' => array_merge(
        $polyfillsBootstraps,
        $polyfillsStubs
    ),

    // See: https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#exposed-symbols
    'expose-global-classes' => false,
    'expose-global-functions' => false,
    'expose-global-constants' => false,
];
