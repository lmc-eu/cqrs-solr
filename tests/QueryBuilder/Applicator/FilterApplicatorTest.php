<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FilterDummyEntity;
use PHPUnit\Framework\Attributes\Test;

class FilterApplicatorTest extends AbstractApplicatorTestCase
{
    private FilterApplicator $filterApplicator;

    protected function setUp(): void
    {
        $this->filterApplicator = new FilterApplicator();
    }

    #[Test]
    public function shouldApplyFilterApplicatorOnQuery(): void
    {
        $entity = new FilterDummyEntity();
        $this->assertTrue($this->filterApplicator->supportEntity($entity));
        $this->filterApplicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(EntityApplicator::class, $entity),
            $this->filterApplicator,
        ]);

        $this->assertStringContainsString('rows=' . $entity->getNumberOfRows(), $queryUri);

        $this->assertStringContainsString(
            sprintf(
                'fq={!tag=%s}%s',
                implode(',', $entity->getTags()),
                $entity->getFilterQuery(),
            ),
            $queryUri,
        );
    }
}
