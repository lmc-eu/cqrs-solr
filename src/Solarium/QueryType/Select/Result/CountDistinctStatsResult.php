<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\Solarium\QueryType\Select\Result;

use Solarium\Component\Result\Stats\Result;

class CountDistinctStatsResult extends Result
{
    /**
     * @return int|null
     */
    public function getCountDistinct()
    {
        return isset($this->stats['countDistinct']) ? (int) $this->stats['countDistinct'] : null;
    }
}
