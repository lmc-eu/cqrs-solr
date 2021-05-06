<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Fixture;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\FacetsInterface;

class FacetsDummyEntity implements FacetsInterface
{
    public function getNumberOfRows(): int
    {
        return 20;
    }

    public function getFields(): array
    {
        return [];
    }

    public function getMinCount(): int
    {
        return 5;
    }

    public function getFacetFields(): array
    {
        return ['facet-field', 'pivot-field', 'interval-field'];
    }

    public function getField(string $facetField): ?string
    {
        if ($facetField === 'facet-field') {
            return 'field';
        }

        return null;
    }

    public function getExcludes(): array
    {
        return ['global-exclude'];
    }

    public function getFacetSetLimit(): int
    {
        return 100;
    }

    public function getFacetFieldsLimit(): int
    {
        return 60;
    }

    public function getPivotFields(string $facetField): ?string
    {
        if ($facetField === 'pivot-field') {
            return 'pivot-field';
        }

        return null;
    }

    public function getIntervalFields(string $facetField): ?array
    {
        if ($facetField === 'interval-field') {
            return [
                [100, 199],
                [200, 300],
            ];
        }

        return null;
    }

    public function getExcludesForFacet(string $facetField): array
    {
        if ($facetField === 'facet-field') {
            return [
                'facet-field-exclude',
            ];
        }

        return [];
    }

    public function getFacetSetSort(): string
    {
        return 'index';
    }
}
