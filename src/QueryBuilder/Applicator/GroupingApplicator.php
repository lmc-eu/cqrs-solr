<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\GroupingInterface;
use Solarium\QueryType\Select\Query\Query;

class GroupingApplicator implements ApplicatorInterface
{
    private GroupingInterface $entity;

    public function supportEntity(EntityInterface $entity): bool
    {
        return $entity instanceof GroupingInterface;
    }

    /** @param GroupingInterface $entity */
    public function setEntity(EntityInterface $entity): void
    {
        $this->entity = $entity;
    }

    public function applyOnQuery(Query $query): void
    {
        $grouping = $query->getGrouping();
        $grouping
            ->addField($this->entity->getGroupingField())
            ->setLimit($this->entity->getGroupingLimit())
            ->setNumberOfGroups($this->entity->getNumberOfGroups())
            ->setMainResult($this->entity->getMainResult());
    }
}
