<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FiltersDummyEntity;

class FiltersApplicatorTest extends ApplicatorTestCase
{
    private FiltersApplicator $filtersApplicator;

    protected function setUp(): void
    {
        $this->filtersApplicator = new FiltersApplicator();
    }

    /**
     * @test
     */
    public function shouldApplyFilterApplicatorOnQuery(): void
    {
        $entity = new FiltersDummyEntity();
        $this->assertTrue($this->filtersApplicator->supportEntity($entity));
        $this->filtersApplicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(EntityApplicator::class, $entity),
            $this->filtersApplicator,
        ]);

        $this->assertStringContainsString('rows=' . $entity->getNumberOfRows(), $queryUri);

        $globalTags = $entity->getTags();

        foreach ($entity->getFilterQueryNames() as $filterQueryName) {
            $tags = $globalTags;

            $tag = $entity->getTagForFilter($filterQueryName);
            if (!empty($tag)) {
                $tags[] = $tag;
            }

            $this->assertStringContainsString(
                sprintf(
                    'fq={!tag=%s}%s',
                    implode(',', $tags),
                    $entity->getFilterQuery($filterQueryName)
                ),
                $queryUri
            );
        }
    }
}
