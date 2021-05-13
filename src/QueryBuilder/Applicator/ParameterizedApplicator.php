<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\ParameterizedInterface;
use Solarium\QueryType\Select\Query\Query;

class ParameterizedApplicator implements ApplicatorInterface
{
    private ParameterizedInterface $entity;

    public function supportEntity(EntityInterface $entity): bool
    {
        return $entity instanceof ParameterizedInterface;
    }

    /** @param ParameterizedInterface $entity */
    public function setEntity(EntityInterface $entity): void
    {
        $this->entity = $entity;
    }

    public function applyOnQuery(Query $query): void
    {
        foreach ($this->entity->getParams() as $param => $value) {
            $query->addParam($param, $value);
        }
    }
}
