<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Fixture;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\StatsInterface;

class StatsDummyEntity implements StatsInterface
{
    public function getNumberOfRows(): int
    {
        return 42;
    }

    public function getFields(): array
    {
        return [];
    }

    public function getStatsFields(): array
    {
        return ['{!countDistinct=true}fieldId', 'anotherIntField'];
    }
}
