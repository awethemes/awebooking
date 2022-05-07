<?php

namespace AweBooking\System\FulltextSearch\Engines;

use InvalidArgumentException;
use MeiliSearch\Client as MeiliSearchClient;

class EngineFactory
{
    public static function create(string $name): Engine
    {
        switch ($name) {
            case 'meilisearch':
                return static::createMeiliSearchEngine();
            default:
                throw new InvalidArgumentException('Unsupported engine');
        }
    }

    public static function createMeiliSearchEngine(): MeiliSearchEngine
    {
        return new MeiliSearchEngine(
            new MeiliSearchClient(
                defined('POD_MEILI_SEARCH_HOST') ? POD_MEILI_SEARCH_HOST : 'http://localhost:7700',
                defined('POD_MEILI_SEARCH_KEY') ? POD_MEILI_SEARCH_KEY : ''
            )
        );
    }
}
