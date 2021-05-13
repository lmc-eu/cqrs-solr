<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\SortInterface;
use Solarium\QueryType\Select\Query\Query;

class SortApplicator implements ApplicatorInterface
{
    private SortInterface $entity;

    public function supportEntity(EntityInterface $entity): bool
    {
        return $entity instanceof SortInterface;
    }

    /** @param SortInterface $entity */
    public function setEntity(EntityInterface $entity): void
    {
        $this->entity = $entity;
    }

    public function applyOnQuery(Query $query): void
    {
        $query->setSorts($this->entity->getSorts());
    }
}
