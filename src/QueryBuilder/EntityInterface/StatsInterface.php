<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\EntityInterface;

interface StatsInterface extends EntityInterface
{
    public function getStatsFields(): array;
}
