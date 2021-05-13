<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Solarium\QueryType\Select\Query\Query;

class EntityApplicator implements ApplicatorInterface
{
    private EntityInterface $entity;

    public function supportEntity(EntityInterface $entity): bool
    {
        return $entity instanceof EntityInterface;
    }

    public function setEntity(EntityInterface $entity): void
    {
        $this->entity = $entity;
    }

    public function applyOnQuery(Query $query): void
    {
        $query
            ->setRows($this->entity->getNumberOfRows())
            ->setFields($this->entity->getFields());
    }
}
