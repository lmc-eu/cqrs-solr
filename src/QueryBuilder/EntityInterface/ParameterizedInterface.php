<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\EntityInterface;

interface ParameterizedInterface extends EntityInterface
{
    public function getParams(): array;
}
