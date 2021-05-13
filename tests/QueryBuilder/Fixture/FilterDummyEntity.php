<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Fixture;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\FilterInterface;

class FilterDummyEntity implements FilterInterface
{
    public function getNumberOfRows(): int
    {
        return 10;
    }

    public function getFields(): array
    {
        return [];
    }

    public function getFilterQuery(): string
    {
        return 'filter:query';
    }

    public function getFilterQueryName(): string
    {
        return 'filterQueryName';
    }

    public function getTags(): array
    {
        return ['filter-tag'];
    }
}
