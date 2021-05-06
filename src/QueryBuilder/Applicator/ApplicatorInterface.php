<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Solarium\QueryType\Select\Query\Query;

interface ApplicatorInterface
{
    public function supportEntity(EntityInterface $entity): bool;

    public function setEntity(EntityInterface $entity): void;

    public function applyOnQuery(Query $query): void;
}
