<?php

namespace AweBooking\System\FulltextSearch\Engines;

use AweBooking\Vendor\Illuminate\Support\Fluent;
use AweBooking\System\FulltextSearch\IndexDefinition;
use AweBooking\System\FulltextSearch\Query;
use AweBooking\System\FulltextSearch\Searchable;

abstract class Engine
{
    /**
     * Update the given model in the index.
     *
     * @param IndexDefinition $index
     * @param \AweBooking\Vendor\Illuminate\Database\Eloquent\Collection $models
     * @return void
     */
    abstract public function update(IndexDefinition $index, $models);

    /**
     * Remove the given model from the index.
     *
     * @param IndexDefinition $indexDefinition
     * @param \AweBooking\Vendor\Illuminate\Database\Eloquent\Collection $models
     * @return void
     */
    abstract public function delete(IndexDefinition $indexDefinition, $models);

    /**
     * Perform the given search on the engine.
     *
     * @param Query $query
     * @return mixed
     */
    abstract public function search(Query $query);

    /**
     * Perform the given search on the engine.
     *
     * @param Query $query
     * @param int $perPage
     * @param int $page
     * @return mixed
     */
    abstract public function paginate(Query $query, $perPage, $page);

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param mixed $results
     * @return array
     */
    abstract public function pluckIds($results): array;

    /**
     * Map the given results to instances of the given model.
     *
     * @param Query $query
     * @param mixed $results
     * @param \AweBooking\Vendor\Illuminate\Database\Eloquent\Model|\AweBooking\Vendor\Illuminate\Support\Fluent $model
     * @return \AweBooking\Vendor\Illuminate\Database\Eloquent\Collection
     */
    abstract public function mapToModel(Query $query, $results, $model);

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param mixed $results
     * @return int
     */
    abstract public function getTotalCount($results);

    /**
     * Flush all of the model's records from the engine.
     *
     * @param string $index
     * @return void
     */
    abstract public function flush($index);

    /**
     * Create a search index.
     *
     * @param string $name
     * @param array $options
     * @return mixed
     */
    abstract public function createIndex($name, array $options = []);

    /**
     * Delete a search index.
     *
     * @param string $name
     * @return mixed
     */
    abstract public function deleteIndex($name);

    /**
     * Pluck and return the primary keys of the given results using the given key name.
     *
     * @param mixed $results
     * @param string $key
     * @return array
     */
    public function pluckIdsFrom($results, $key): array
    {
        return $this->pluckIds($results);
    }

    /**
     * Get the results of the query as an array of primary keys.
     *
     * @param Query $query
     * @return array
     */
    public function keys(Query $query): array
    {
        return $this->pluckIds($this->search($query));
    }

    /**
     * Get the results of the given query mapped onto models.
     *
     * @param Query $query
     * @return \AweBooking\Vendor\Illuminate\Database\Eloquent\Collection
     */
    public function get(Query $query)
    {
        return $this->mapToModel(
            $query,
            $this->search($query),
            $query->model
        );
    }

    /**
     * @param mixed $model
     * @return array|null
     */
    protected function getSearchableArray($model): ?array
    {
        if ($model instanceof Searchable) {
            return $model->toSearchableArray();
        }

        if ($model instanceof Fluent) {
            return $model->getAttributes();
        }

        return null;
    }
}
