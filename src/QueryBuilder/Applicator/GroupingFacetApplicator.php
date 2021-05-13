<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\GroupingFacetInterface;
use Solarium\QueryType\Select\Query\Query;

class GroupingFacetApplicator implements ApplicatorInterface
{
    private GroupingFacetInterface $entity;

    public function supportEntity(EntityInterface $entity): bool
    {
        return $entity instanceof GroupingFacetInterface;
    }

    /** @param GroupingFacetInterface $entity */
    public function setEntity(EntityInterface $entity): void
    {
        $this->entity = $entity;
    }

    public function applyOnQuery(Query $query): void
    {
        $query
            ->getGrouping()
            ->setFacet($this->entity->getGroupingFacet());
    }
}
