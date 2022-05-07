<?php

namespace AweBooking\System\FulltextSearch\Engines;

use AweBooking\Vendor\Illuminate\Database\Eloquent\SoftDeletes;
use AweBooking\Vendor\Illuminate\Support\Fluent;
use MeiliSearch\Client as MeiliSearchClient;
use MeiliSearch\MeiliSearch;
use MeiliSearch\Search\SearchResult;
use AweBooking\System\Database\Model;
use AweBooking\System\FulltextSearch\IndexDefinition;
use AweBooking\System\FulltextSearch\Query;

use function AweBooking\System\class_uses_recursive;
use function AweBooking\System\collect;

class MeiliSearchEngine extends Engine
{
    /**
     * The MeiliSearch client.
     *
     * @var \MeiliSearch\Client
     */
    protected $meiliSearch;

    /**
     * Determines if soft deletes for Scout are enabled or not.
     *
     * @var bool
     */
    protected $softDelete;

    /**
     * Create a new MeiliSearchEngine instance.
     *
     * @param \MeiliSearch\Client $meiliSearch
     * @param bool $softDelete
     * @return void
     */
    public function __construct(MeiliSearchClient $meiliSearch, bool $softDelete = false)
    {
        $this->meiliSearch = $meiliSearch;
        $this->softDelete = $softDelete;
    }

    /**
     * {@inheritdoc}
     */
    public function update(IndexDefinition $indexDefinition, $models)
    {
        if ($models->isEmpty()) {
            return;
        }

        $index = $this->meiliSearch->index($indexDefinition->getIndexName());

        if ($this->usesSoftDelete($models->first()) && $this->softDelete) {
            // $models->each->pushSoftDeleteMetadata();
        }

        $objects = $models->map(function ($model) use ($indexDefinition) {
            if (empty($searchableData = $this->getSearchableArray($model))) {
                return null;
            }

            return array_merge(
                [$model->getKeyName() => $model->getAttribute($indexDefinition->getModelKeyName())],
                $searchableData,
                // $model->fullTextSearchMetadata()
            );
        })->filter()->values()->all();

        if (!empty($objects)) {
            $index->addDocuments($objects, $models->first()->getKeyName());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(IndexDefinition $indexDefinition, $models)
    {
        $index = $this->meiliSearch->index($indexDefinition->getIndexName());

        $index->deleteDocuments(
            $models->map(function ($model) use ($indexDefinition) {
                return $indexDefinition->getModelKey($model);
            })->values()->all(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function search(Query $query)
    {
        return $this->performSearch(
            $query,
            array_filter([
                'filters' => $this->parseFilters($query),
                'limit' => $query->limit,
                'sort' => $this->buildSortFromOrderByClauses($query),
            ])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(Query $query, $perPage, $page)
    {
        return $this->performSearch(
            $query,
            array_filter([
                'filters' => $this->parseFilters($query),
                'limit' => (int) $perPage,
                'offset' => ($page - 1) * $perPage,
                'sort' => $this->buildSortFromOrderByClauses($query),
            ])
        );
    }

    protected function performSearch(Query $query, array $searchParams = [])
    {
        $meilisearch = $this->meiliSearch->index(
            $query->index ?: $query->indexDefinition()->getIndexName()
        );

        // meilisearch-php 0.19.0 is compatible with meilisearch server 0.21.0...
        if (
            isset($searchParams['filters'])
            && version_compare(MeiliSearch::VERSION, '0.19.0') >= 0
        ) {
            $searchParams['filter'] = $searchParams['filters'];

            unset($searchParams['filters']);
        }

        if ($query->callback) {
            $result = call_user_func(
                $query->callback,
                $meilisearch,
                $query->query,
                $searchParams
            );

            return $result instanceof SearchResult ? $result->getRaw() : $result;
        }

        return $meilisearch->rawSearch($query->query, $searchParams);
    }

    protected function parseFilters(Query $query): string
    {
        $filters = collect($query->wheres)->map(function ($value, $key) {
            if (is_bool($value)) {
                return sprintf('%s=%s', $key, $value ? 'true' : 'false');
            }

            return is_numeric($value)
                ? sprintf('%s=%s', $key, $value)
                : sprintf('%s="%s"', $key, $value);
        });

        foreach ($query->whereIns as $key => $values) {
            $filters->push(
                sprintf(
                    '(%s)',
                    collect($values)->map(function ($value) use ($key) {
                        if (is_bool($value)) {
                            return sprintf('%s=%s', $key, $value ? 'true' : 'false');
                        }

                        return filter_var($value, FILTER_VALIDATE_INT) !== false
                            ? sprintf('%s=%s', $key, $value)
                            : sprintf('%s="%s"', $key, $value);
                    })->values()->implode(' OR ')
                )
            );
        }

        return $filters->values()->implode(' AND ');
    }

    protected function buildSortFromOrderByClauses(Query $query): array
    {
        return collect($query->orders)->map(function (array $order) {
            return $order['column'] . ':' . $order['direction'];
        })->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function pluckIds($results): array
    {
        if (0 === count($results['hits'])) {
            return [];
        }

        $hits = collect($results['hits']);

        $key = key($hits->first());

        return $hits->pluck($key)->values()->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function pluckIdsFrom($results, $key): array
    {
        return count($results['hits']) === 0
            ? []
            : array_column($results['hits'], $key);
    }

    /**
     * {@inheritdoc}
     */
    public function mapToModel(Query $query, $results, $model)
    {
        if ($model instanceof Fluent) {
        }

        if (is_null($results) || 0 === count($results['hits'])) {
            return $model->newCollection();
        }

        $objectIds = collect($results['hits'])->pluck($model->getKeyName())->values()->all();

        $objectIdPositions = array_flip($objectIds);

        return $query->queryDatabaseModelsByIds($objectIds)
            ->get()
            ->filter(function ($model) use ($query, $objectIds) {
                $searchKey = $model->getAttribute($query->indexDefinition()->getModelKeyName());

                return in_array($searchKey, $objectIds, false);
            })->sortBy(function ($model) use ($query, $objectIdPositions) {
                $searchKey = $model->getAttribute($query->indexDefinition()->getModelKeyName());

                return $objectIdPositions[$searchKey];
            })->values();
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount($results)
    {
        return $results['nbHits'];
    }

    /**
     * {@inheritdoc}
     */
    public function flush($index)
    {
        $indexes = $this->meiliSearch->index($index);

        $indexes->deleteAllDocuments();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \MeiliSearch\Exceptions\ApiException
     */
    public function createIndex($name, array $options = [])
    {
        return $this->meiliSearch->createIndex($name, $options);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \MeiliSearch\Exceptions\ApiException
     */
    public function deleteIndex($name)
    {
        return $this->meiliSearch->deleteIndex($name);
    }

    /**
     * Dynamically call the MeiliSearch client instance.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->meiliSearch->$method(...$parameters);
    }
}
