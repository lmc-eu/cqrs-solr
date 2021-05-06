<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\Fixture\SortDummyEntity;

class SortApplicatorTest extends ApplicatorTestCase
{
    private SortApplicator $sortApplicator;

    protected function setUp(): void
    {
        $this->sortApplicator = new SortApplicator();
    }

    /**
     * @test
     */
    public function shouldApplySortOnQuery(): void
    {
        $entity = new SortDummyEntity();
        $this->assertTrue($this->sortApplicator->supportEntity($entity));
        $this->sortApplicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(EntityApplicator::class, $entity),
            $this->sortApplicator,
        ]);

        $this->assertStringContainsString('rows=' . $entity->getNumberOfRows(), $queryUri);

        $sortsExpects = [];
        foreach ($entity->getSorts() as $sortField => $sortType) {
            $sortsExpects[] = sprintf('%s %s', $sortField, $sortType);
        }
        $this->assertStringContainsString('sort=' . implode(',', $sortsExpects), $queryUri);
    }
}
