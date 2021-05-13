<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\Fixture\GroupingFacetDummyEntity;

class GroupingFacetApplicatorTest extends ApplicatorTestCase
{
    private GroupingFacetApplicator $groupingFacetApplicator;

    protected function setUp(): void
    {
        $this->groupingFacetApplicator = new GroupingFacetApplicator();
    }

    /**
     * @test
     */
    public function shouldApplyGroupingOnQuery(): void
    {
        $entity = new GroupingFacetDummyEntity();
        $this->assertTrue($this->groupingFacetApplicator->supportEntity($entity));
        $this->groupingFacetApplicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(EntityApplicator::class, $entity),
            $this->getApplicator(FacetsApplicator::class, $entity),
            $this->getApplicator(GroupingApplicator::class, $entity),
            $this->groupingFacetApplicator,
        ]);

        $this->assertStringContainsString('rows=' . $entity->getNumberOfRows(), $queryUri);

        $this->assertStringContainsString('group=true', $queryUri);
        $this->assertStringContainsString('group.main=' . ($entity->getMainResult() ? 'true' : 'false'), $queryUri);
        $this->assertStringContainsString('group.field=' . $entity->getGroupingField(), $queryUri);
        $this->assertStringContainsString('group.limit=' . $entity->getGroupingLimit(), $queryUri);
        $this->assertStringContainsString(
            'group.ngroups=' . ($entity->getNumberOfGroups() ? 'true' : 'false'),
            $queryUri
        );

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

        $this->assertStringContainsString('group.facet=true', $queryUri);
    }
}
