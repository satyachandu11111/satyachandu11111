<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-search-ultimate
 * @version   2.0.33
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\SearchElastic\InstantProvider;

use Elasticsearch\Client;
use Mirasvit\SearchAutocomplete\InstantProvider\InstantProvider;

class EngineProvider extends InstantProvider
{
    private $query          = [];

    private $activeFilters  = [];

    private $applyFilter    = false;

    private $filtersToApply = [];

    public function getResults(string $indexIdentifier): array
    {
        $this->query = [
            'index' => $this->configProvider->getIndexName($indexIdentifier),
            'body'  => [
                'from'          => 0,
                'size'          => $this->getLimit($indexIdentifier),
                'stored_fields' => [
                    '_id',
                    '_score',
                    '_source',
                ],
                'sort'          => [
                    [
                        '_score' => [
                            'order' => 'desc',
                        ],
                    ],
                ],
                'query'         => [
                    'bool' => [
                        'minimum_should_match' => 1,
                    ],
                ],
            ],
        ];

        $this->setMustCondition($indexIdentifier);
        $this->setShouldCondition($indexIdentifier);

        if ($indexIdentifier === 'catalogsearch_fulltext') {
            $this->setBuckets();
        }

        try {
            $rawResponse = $this->getClient()->search($this->query);
        } catch (\Exception $e) {
            return [
                'totalItems' => 0,
                'items'      => [],
                'buckets'    => [],
            ];
        }

        if ($this->configProvider->getEngine() == 'elasticsearch6') {
            $totalItems = (int)$rawResponse['hits']['total'];
        } else {
            $totalItems = (int)$rawResponse['hits']['total']['value'];
        }

        $items = [];

        foreach ($rawResponse['hits']['hits'] as $data) {
            if (!isset($data['_source']['_instant'])) {
                continue;
            }

            if (!$data['_source']['_instant']) {
                continue;
            }

            $items[] = $data['_source']['_instant'];
        }

        $buckets = [];

        if (isset($rawResponse['aggregations'])) {
            foreach ($rawResponse['aggregations'] as $code => $data) {
                $bucketData = $this->configProvider->getBucketOptionsData($code, $data['buckets']);
                if (empty($bucketData)) {
                    continue;
                }

                $buckets[$code] = $bucketData;
            }
        }

        if (!empty($this->getActiveFilters()) && $this->applyFilter == false) {
            $this->applyFilter = true;
            foreach ($this->getActiveFilters() as $filterKey => $value) {
                $this->filtersToApply[] = $filterKey;

                $result = $this->getResults($indexIdentifier);
                foreach ($result['buckets'] as $bucketKey => $bucket) {
                    if (in_array($bucketKey, $this->filtersToApply)) {
                        continue;
                    }

                    $buckets[$bucketKey] = $bucket;
                }

                $totalItems = $result['totalItems'];
                $items      = $result['items'];

            }
        }

        return [
            'totalItems' => count($items) > 0 ? $totalItems : 0,
            'items'      => $items,
            'buckets'    => $buckets,
        ];
    }

    private function getActiveFilters(): array
    {
        if (empty($this->activeFilters)) {
            $this->activeFilters = $this->configProvider->getActiveFilters();
        }

        if (!empty($this->filtersToApply)) {
            return array_intersect_key($this->activeFilters, array_flip($this->filtersToApply));
        }

        return $this->activeFilters;
    }

    private function setMustCondition(string $indexIdentifier): void
    {
        if ($indexIdentifier === 'catalogsearch_fulltext') {
            $this->query['body']['query']['bool']['must'][] = [
                'terms' => [
                    'visibility' => ['3', '4'],
                ],
            ];

            if ($this->applyFilter) {
                foreach ($this->getActiveFilters() as $filterCode => $filterValue) {
                    $this->query['body']['query']['bool']['must'][] = [
                        'term' => [
                            $filterCode => $filterValue,
                        ],
                    ];
                }
            }
        }
    }

    private function setShouldCondition(string $indexIdentifier): void
    {
        $fields          = $this->configProvider->getIndexFields($indexIdentifier);
        $fields['_misc'] = 1;

        $searchQuery = $this->queryService->build($this->getQueryText());
        $queryBody   = [];

        foreach ($fields as $resolvedField => $boost) {
            $boost = (int)($match['boost'] ?? 1);

            if ($resolvedField === '_search') {
                $resolvedField = '_misc';
            }

            $q = $this->compileQuery($searchQuery, $resolvedField, $boost);

            if ($q) {
                $queryBody = array_merge_recursive($queryBody, $q);
            }
        }

        if (!isset($this->query['body']['query']['bool'])) {
            $this->query['body']['query']['bool'] = [];
        }

        $this->query['body']['query']['bool'] = array_merge($this->query['body']['query']['bool'], $queryBody);
    }

    private function setBuckets(): void
    {
        foreach ($this->getBuckets() as $fieldName) {
            if ($this->applyFilter && in_array($fieldName, $this->filtersToApply)) {
                continue;
            }

            $this->query['body']['aggregations'][$fieldName] = ['terms' => ['field' => $fieldName]];
        }
    }

    private function compileQuery(array $query, string $field, int $boost, bool $isNotLike = false): array
    {
        $compiled = [];
        foreach ($query as $directive => $value) {
            switch ($directive) {
                case '$like':
                    $compiled['should'][] = $this->compileQuery($value, $field, $boost, false);
                    break;

                case '$!like':
                    $q = $this->compileQuery($value, $field, $boost, true);

                    $compiled['must_not'] = $q;
                    break;

                case '$and':
                    $and = [];
                    foreach ($value as $item) {
                        $and[] = $this->compileQuery($item, $field, $boost, $isNotLike);
                    }
                    if (count($and)) {
                        if ($isNotLike) {
                            $compiled = $and;
                        } else {
                            $compiled['bool']['must'] = $and;
                        }
                    }
                    break;

                case '$or':
                    $or = [];
                    foreach ($value as $item) {
                        $or[] = $this->compileQuery($item, $field, $boost, $isNotLike);
                    }

                    if (count($or)) {
                        if ($isNotLike) {
                            $compiled = $and;
                        } else {
                            $compiled['bool']['should'] = $or;
                        }
                    }

                    break;

                case '$term':
                    $phrase = $this->escape($value['$phrase']);

                    switch ($value['$wildcard']) {
                        case $this->configProvider::WILDCARD_INFIX:
                            $compiled['bool']['should'][]['wildcard'] = [
                                $field => [
                                    'value' => "*$phrase*",
                                    'boost' => $boost,
                                ],
                            ];

                            $compiled['bool']['should'][]['wildcard'] = [
                                $field . '.keyword' => [
                                    'value' => "*$phrase*",
                                    'boost' => $boost,
                                ],
                            ];
                            break;

                        case $this->configProvider::WILDCARD_PREFIX:
                            $compiled['bool']['should'][]['wildcard'] = [
                                $field => [
                                    'value' => "*$phrase",
                                    'boost' => $boost,
                                ],
                            ];

                            $compiled['bool']['should'][]['wildcard'] = [
                                $field . '.keyword' => [
                                    'value' => "*$phrase",
                                    'boost' => $boost,
                                ],
                            ];
                            break;

                        case $this->configProvider::WILDCARD_SUFFIX:
                            $compiled['bool']['should'][]['wildcard'] = [
                                $field => [
                                    'value' => "$phrase*",
                                    'boost' => $boost,
                                ],
                            ];

                            $compiled['bool']['should'][]['wildcard'] = [
                                $field . '.keyword' => [
                                    'value' => "$phrase*",
                                    'boost' => $boost,
                                ],
                            ];
                            break;

                        case $this->configProvider::WILDCARD_DISABLED:
                            $compiled['bool']['should'][]['match_phrase'] = [
                                $field => [
                                    'query' => $value['$phrase'],
                                    'boost' => $boost,
                                ],
                            ];
                            break;
                    }
                    break;
            }
        }

        return $compiled;
    }

    private function getClient(): Client
    {
        return \Elasticsearch\ClientBuilder::fromConfig($this->configProvider->getEngineConnection(), true);
    }
}
