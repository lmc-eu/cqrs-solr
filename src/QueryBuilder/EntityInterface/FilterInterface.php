<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\EntityInterface;

interface FilterInterface extends EntityInterface
{
    public function getFilterQuery(): string;

    public function getFilterQueryName(): string;

    public function getTags(): array;
}
