<?php

namespace AweBooking\System\FulltextSearch;

use InvalidArgumentException;
use AweBooking\Vendor\Illuminate\Database\Eloquent\Model;
use AweBooking\Vendor\Illuminate\Support\Fluent;
use AweBooking\Vendor\Illuminate\Contracts\Events\Dispatcher;
use AweBooking\System\Container;
use AweBooking\System\FulltextSearch\Engines\Engine;

class FulltextSearch
{
    /**
     * @var Engine
     */
    protected $engine;

    /**
     * @var Dispatcher
     */
    protected $eventDispatcher;

    /**
     * @var array<string, IndexDefinition>
     */
    protected $indexes = [];

    /**
     * @var array<string, string>
     */
    protected $modelsIndexed = [];

    /**
     * @param Engine $engine
     * @param Dispatcher|null $eventDispatcher
     */
    public function __construct(Engine $engine, Dispatcher $eventDispatcher = null)
    {
        $this->engine = $engine;
        $this->eventDispatcher =
            $eventDispatcher ?? Container::getInstance()->get(Dispatcher::class);
    }

    /**
     * Get the engine instance.
     *
     * @return Engine
     */
    public function engine(): Engine
    {
        return $this->engine;
    }

    /**
     * Get the indexes definition.
     *
     * @return IndexDefinition[]
     */
    public function indexes(): array
    {
        return $this->indexes;
    }

    /**
     * @param string $indexName
     * @return IndexDefinition
     */
    public function getIndex(string $indexName): IndexDefinition
    {
        if (!isset($this->indexes[$indexName]) || !isset($this->modelsIndexed[$indexName])) {
            throw new InvalidArgumentException("Index '{$indexName}' does not exist.");
        }

        return $this->indexes[$indexName] ?? $this->modelsIndexed[$indexName];
    }

    /**
     * @param string $name
     * @param string $model
     * @return IndexDefinition
     */
    public function register(string $name, string $model): IndexDefinition
    {
        $index = $this->indexes[$name] = new IndexDefinition($name, $model);

        if (is_subclass_of($model, Model::class)) {
            $this->modelsIndexed[$model] = $this->indexes[$name];
        }

        return $index;
    }

    /**
     * Query full-text search.
     *
     * @param string $query
     * @param string $index
     * @return Query
     */
    public function query(string $query, string $index): Query
    {
        $indexDefinition = $this->getIndex($index);

        $isEloquentModel = is_subclass_of($indexDefinition->model, Model::class);

        return new Query(
            $this->engine,
            $indexDefinition,
            $isEloquentModel ? new $indexDefinition->model : new Fluent(),
            $query
        );
    }

    /**
     * @param array|null $indexes
     * @return void
     */
    public function createIndexes($indexes = null): void
    {
        $indexes = $indexes ?? array_keys($this->indexes);

        foreach ((array) $indexes as $index) {
            $this->engine->createIndex($index);
        }
    }

    /**
     * @param array|null $indexes
     * @return void
     */
    public function deleteIndexes($indexes = null): void
    {
        $indexes = $indexes ?? array_keys($this->indexes);

        foreach ((array) $indexes as $index) {
            $this->engine->deleteIndex($index);
        }
    }

    public function makeSearchable(string $index, $queryCallback = null, $chunkSize = 500): void
    {
        $indexDefinition = $this->getIndex($index);

        /** @var \AweBooking\Vendor\Illuminate\Database\Eloquent\Builder $builder */
        $builder = $indexDefinition->model::query();

        if ($queryCallback) {
            $queryCallback($builder);
        }

        $builder->chunkById($chunkSize, function ($models) use ($indexDefinition) {
            $this->queueMakeSearchable(
                $indexDefinition,
                $models->filter(function ($model) {
                    return method_exists($model, 'isSearchable') ? $model->isSearchable() : true;
                })
            );
            // event(new ModelsImported($models));
        });
    }

    /**
     * Dispatch the job to make the given models searchable.
     *
     * @param IndexDefinition $indexDefinition
     * @param \AweBooking\Vendor\Illuminate\Database\Eloquent\Collection $models
     * @return void
     */
    public function queueMakeSearchable(IndexDefinition $indexDefinition, $models)
    {
        if ($models->isEmpty()) {
            return;
        }

        $this->engine->update($indexDefinition, $models);

        // if (!config('scout.queue')) {
        // }

        // dispatch((new Scout::$makeSearchableJob($models))
        //     ->onQueue($models->first()->syncWithSearchUsingQueue())
        //     ->onConnection($models->first()->syncWithSearchUsing()));
    }
}
