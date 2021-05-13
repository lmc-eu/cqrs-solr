<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\EntityInterface;

interface FacetsInterface extends EntityInterface
{
    public function getMinCount(): int;

    public function getFacetFields(): array;

    public function getField(string $facetField): ?string;

    public function getExcludes(): array;

    public function getFacetSetLimit(): int;

    public function getFacetFieldsLimit(): int;

    public function getPivotFields(string $facetField): ?string;

    public function getIntervalFields(string $facetField): ?array;

    public function getExcludesForFacet(string $facetField): array;

    public function getFacetSetSort(): string;
}
