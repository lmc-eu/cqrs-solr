<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\EntityInterface;

interface FiltersInterface extends EntityInterface
{
    public function getFilterQueryNames(): array;

    public function getFilterQuery(string $filterQueryName): string;

    public function getTags(): array;

    public function getTagForFilter(string $filterQueryName): ?string;
}
