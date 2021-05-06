<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\FacetsInterface;
use Solarium\Component\Facet\AbstractFacet;
use Solarium\Component\Facet\FacetInterface;
use Solarium\Component\Facet\Field;
use Solarium\Component\Facet\MultiQuery;
use Solarium\Component\Facet\Pivot;
use Solarium\Component\FacetSet;
use Solarium\QueryType\Select\Query\Query;

class FacetsApplicator implements ApplicatorInterface
{
    private FacetsInterface $entity;

    public function supportEntity(EntityInterface $entity): bool
    {
        return $entity instanceof FacetsInterface;
    }

    /** @param FacetsInterface $entity */
    public function setEntity(EntityInterface $entity): void
    {
        $this->entity = $entity;
    }

    public function applyOnQuery(Query $query): void
    {
        $facetSet = $query->getFacetSet();

        $this
            ->applyDefaults($facetSet)
            ->applySort($facetSet)
            ->createFacets($facetSet);
    }

    private function applyDefaults(FacetSet $facetSet): self
    {
        $facetSet
            ->setMinCount($this->entity->getMinCount())
            ->setLimit($this->entity->getFacetSetLimit());

        return $this;
    }

    private function applySort(FacetSet $facetSet): self
    {
        $sort = $this->entity->getFacetSetSort();
        if (!empty($sort)) {
            $facetSet->setSort($sort);
        }

        return $this;
    }

    private function createFacets(FacetSet $facetSet): self
    {
        $excludes = $this->entity->getExcludes();

        foreach ($this->entity->getFacetFields() as $facetField) {
            $pivotFields = $this->entity->getPivotFields($facetField);
            $intervalFields = $this->entity->getIntervalFields($facetField);

            if ($pivotFields !== null) {
                $field = $this->buildPivotFacet($facetSet, $facetField, $pivotFields);
            } elseif ($intervalFields !== null) {
                $field = $this->buildIntervalFacet($facetSet, $facetField, $intervalFields);
            } else {
                $field = $this->buildFacet($facetSet, $facetField);
            }

            if ($field instanceof AbstractFacet) {
                $this->setExcludes($field, $facetField, $excludes);
            }
        }

        return $this;
    }

    private function buildPivotFacet(FacetSet $facetSet, string $facetField, string $pivotFields): FacetInterface
    {
        $facet = $facetSet->createFacetPivot($facetField);

        if ($facet instanceof Pivot) {
            $facet = $facet->addFields($pivotFields);
        }

        return $facet;
    }

    private function buildIntervalFacet(FacetSet $facetSet, string $facetField, iterable $ranges): FacetInterface
    {
        $facet = $facetSet->createFacetMultiQuery($facetField);

        if ($facet instanceof MultiQuery) {
            foreach ($ranges as $range) {
                $from = $range[0];
                $to = $range[1];

                $facet = $facet->createQuery(
                    sprintf('%s-%s', $from, $to),
                    sprintf('%s:[%s TO %s]', $facetField, $from, $to)
                );
            }
        }

        return $facet;
    }

    private function buildFacet(FacetSet $facetSet, string $facetField): FacetInterface
    {
        $field = $facetSet->createFacetField($facetField);

        if ($field instanceof Field) {
            $facetFieldValue = $this->entity->getField($facetField);

            if ($facetFieldValue !== null) {
                $field->setField($facetFieldValue);
            }

            $field->setLimit($this->entity->getFacetFieldsLimit());
        }

        return $field;
    }

    private function setExcludes(AbstractFacet $field, string $facetField, array $facetExcludes): void
    {
        foreach ($this->entity->getExcludesForFacet($facetField) as $exclude) {
            if (!empty($exclude)) {
                $facetExcludes[] = $exclude;
            }
        }

        if (!empty($facetExcludes) && method_exists($field, 'setExcludes')) {
            $field->setExcludes($facetExcludes);
        }
    }
}
