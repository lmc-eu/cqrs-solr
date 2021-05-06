<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\EntityInterface;

interface GroupingFacetInterface extends GroupingInterface, FacetsInterface
{
    public function getGroupingFacet(): bool;
}
