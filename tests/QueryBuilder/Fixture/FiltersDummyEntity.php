<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Fixture;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\FiltersInterface;

class FiltersDummyEntity implements FiltersInterface
{
    public function getNumberOfRows(): int
    {
        return 20;
    }

    public function getFields(): array
    {
        return [];
    }

    public function getFilterQueryNames(): array
    {
        return ['filter-1', 'filter-2'];
    }

    public function getFilterQuery(string $filterQueryName): string
    {
        if ($filterQueryName === 'filter-1') {
            return 'fitler1:"value"';
        } elseif ($filterQueryName === 'filter-2') {
            return 'fitler2:"value"';
        }

        return '';
    }

    public function getTags(): array
    {
        return ['global-tags'];
    }

    public function getTagForFilter(string $filterQueryName): ?string
    {
        if ($filterQueryName === 'filter-1') {
            return 'filter-1-tag';
        }

        return null;
    }
}
