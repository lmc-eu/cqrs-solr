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

        $this->assertStringContainsString('rows=' . $entity->getNumberOfRows(), $queryUri);

        $this->assertStringContainsString('facet=true', $queryUri);
        $this->assertStringContainsString('facet.sort=' . $entity->getFacetSetSort(), $queryUri);
        $this->assertStringContainsString('facet.mincount=' . $entity->getMinCount(), $queryUri);
        $this->assertStringContainsString('facet.limit=' . $entity->getFacetSetLimit(), $queryUri);
        $this->assertStringContainsString('f.field.facet.limit=' . $entity->getFacetFieldsLimit(), $queryUri);

        $facetFields = $entity->getFacetFields();
        $globalExcludes = implode(',', $entity->getExcludes());

        $facetField = $facetFields[0];
        $this->assertStringContainsString(
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
        $this->assertStringContainsString(
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
            $this->assertStringContainsString(
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
