<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\EntityInterface;

interface EntityInterface
{
    public function getNumberOfRows(): int;

    public function getFields(): array;
}
