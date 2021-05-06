<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\Solarium\QueryType\Select\Query\Component;

use Lmc\Cqrs\Solr\Solarium\QueryType\Select\ResponseParser\CountDistinctStatsResponseParser;
use Solarium\Component\ResponseParser\ComponentParserInterface;
use Solarium\Component\Stats\Stats;

class CountDistinctStatsComponent extends Stats
{
    /**
     * @return ComponentParserInterface|CountDistinctStatsResponseParser
     */
    public function getResponseParser(): ?ComponentParserInterface
    {
        return new CountDistinctStatsResponseParser();
    }
}
