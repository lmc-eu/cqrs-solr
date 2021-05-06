<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\StatsInterface;
use Lmc\Cqrs\Solr\Solarium\QueryType\Select\Query\Component\CountDistinctStatsComponent;
use Solarium\QueryType\Select\Query\Query;

class StatsApplicator implements ApplicatorInterface
{
    private StatsInterface $entity;

    public function supportEntity(EntityInterface $entity): bool
    {
        return $entity instanceof StatsInterface;
    }

    /** @param StatsInterface $entity */
    public function setEntity(EntityInterface $entity): void
    {
        $this->entity = $entity;
    }

    public function applyOnQuery(Query $query): void
    {
        // override the default Stats component with our component with a method to retrieve the countDistinct value
        $query->registerComponentType(Query::COMPONENT_STATS, CountDistinctStatsComponent::class);

        $stats = $query->getStats();

        foreach ($this->entity->getStatsFields() as $field) {
            $stats->createField($field);
        }
    }
}
