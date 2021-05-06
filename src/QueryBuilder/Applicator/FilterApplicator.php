<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\FilterInterface;
use Solarium\QueryType\Select\Query\Query;

class FilterApplicator implements ApplicatorInterface
{
    private FilterInterface $entity;

    public function supportEntity(EntityInterface $entity): bool
    {
        return $entity instanceof FilterInterface;
    }

    /**
     * @param FilterInterface $entity
     */
    public function setEntity(EntityInterface $entity): void
    {
        $this->entity = $entity;
    }

    public function applyOnQuery(Query $query): void
    {
        $filterQuery = $query
            ->createFilterQuery($this->entity->getFilterQueryName())
            ->setQuery($this->entity->getFilterQuery());

        $tags = $this->entity->getTags();

        if (!empty($tags)) {
            $filterQuery->addTags($tags);
        }

        $query->addFilterQuery($filterQuery);
    }
}
