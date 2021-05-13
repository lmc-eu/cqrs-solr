<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\Solarium\QueryType\Select\ResponseParser;

use Lmc\Cqrs\Solr\Solarium\QueryType\Select\Result\CountDistinctStatsResult;
use Solarium\Component\ResponseParser\ComponentParserInterface;
use Solarium\Component\Result\Stats\FacetValue;
use Solarium\Component\Result\Stats\Stats;
use Solarium\QueryType\Select\Query\Query;

/**
 * This class is a copy of the \Solarium\QueryType\Select\ResponseParser\Component\Stats class.
 * Only difference is the use of CountDistinctStatsResult class as our implementation of result class.
 */
class CountDistinctStatsResponseParser implements ComponentParserInterface
{
    /**
     * @param Query $query
     * @param object $stats
     * @param array $data
     * @return Stats
     */
    public function parse($query, $stats, $data)
    {
        $results = [];

        if (isset($data['stats']['stats_fields'])) {
            $statResults = $data['stats']['stats_fields'];

            foreach ($statResults as $field => $statResult) {
                if (isset($statResult['facets'])) {
                    foreach ($statResult['facets'] as $facetField => $values) {
                        foreach ($values as $value => $valueStats) {
                            $statResult['facets'][$facetField][$value] = new FacetValue($value, $valueStats);
                        }
                    }
                }

                $results[$field] = new CountDistinctStatsResult($field, $statResult);
            }
        }

        return new Stats($results);
    }
}
