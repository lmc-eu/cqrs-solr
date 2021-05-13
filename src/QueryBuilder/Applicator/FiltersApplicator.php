<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\FiltersInterface;
use Solarium\QueryType\Select\Query\Query;

class FiltersApplicator implements ApplicatorInterface
{
    private FiltersInterface $entity;

    public function supportEntity(EntityInterface $entity): bool
    {
        return $entity instanceof FiltersInterface;
    }

    /**
     * @param FiltersInterface $entity
     */
    public function setEntity(EntityInterface $entity): void
    {
        $this->entity = $entity;
    }

    public function applyOnQuery(Query $query): void
    {
        $filterQueryNames = $this->entity->getFilterQueryNames();
        $tags = $this->entity->getTags();

        foreach ($filterQueryNames as $filterQueryName) {
            $tag = $this->entity->getTagForFilter($filterQueryName);
            $filterQuery = $query
                ->createFilterQuery($filterQueryName)
                ->setQuery($this->entity->getFilterQuery($filterQueryName));

            if (!empty($tags)) {
                $filterQuery->addTags($tags);
            }
            if (!empty($tag)) {
                $filterQuery->addTag($tag);
            }

            $query->addFilterQuery($filterQuery);
        }
    }
}
