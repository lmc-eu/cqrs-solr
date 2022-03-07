<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FacetsDummyEntity;

class FacetsApplicatorTest extends ApplicatorTestCase
{
    private FacetsApplicator $facetsApplicator;

    protected function setUp(): void
    {
        $this->facetsApplicator = new FacetsApplicator();
    }

    /**
     * @test
     */
    public function shouldApplyFacetsOnQuery(): void
    {
        $entity = new FacetsDummyEntity();
        $this->assertTrue($this->facetsApplicator->supportEntity($entity));
        $this->facetsApplicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(EntityApplicator::class, $entity),
            $this->facetsApplicator,
        ]);

        $this->assertQueryStringContainsPart('rows=' . $entity->getNumberOfRows(), $queryUri);

        $this->assertQueryStringContainsPart('facet=true', $queryUri);
        $this->assertQueryStringContainsPart('facet.sort=' . $entity->getFacetSetSort(), $queryUri);
        $this->assertQueryStringContainsPart('facet.mincount=' . $entity->getMinCount(), $queryUri);
        $this->assertQueryStringContainsPart('facet.limit=' . $entity->getFacetSetLimit(), $queryUri);
        $this->assertQueryStringContainsPart('f.field.facet.limit=' . $entity->getFacetFieldsLimit(), $queryUri);

        $facetFields = $entity->getFacetFields();
        $globalExcludes = implode(',', $entity->getExcludes());

        $facetField = $facetFields[0];
        $this->assertQueryStringContainsPart(
            sprintf(
                'facet.field={!key=%s ex=%s,%s}%s',
                $facetField,
                $globalExcludes,
                implode(',', $entity->getExcludesForFacet($facetField)),
                $entity->getField($facetField)
            ),
            $queryUri
        );

        $pivotField = $facetFields[1];
        $this->assertQueryStringContainsPart(
            sprintf(
                'facet.pivot={!key=%s ex=%s}%s',
                $pivotField,
                $globalExcludes,
                $entity->getPivotFields($pivotField)
            ),
            $queryUri
        );

        $intervalField = $facetFields[2];
        $intervalFields = (array) $entity->getIntervalFields($intervalField);

        foreach ($intervalFields as $range) {
            $this->assertQueryStringContainsPart(
                sprintf(
                    'facet.query={!key=%s}%s:[%s TO %s]',
                    implode('-', $range),
                    $intervalField,
                    $range[0],
                    $range[1]
                ),
                $queryUri
            );
        }
    }
}
