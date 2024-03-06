<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\Fixture\StatsDummyEntity;
use PHPUnit\Framework\Attributes\Test;

class StatsApplicatorTest extends AbstractApplicatorTestCase
{
    private StatsApplicator $applicator;

    protected function setUp(): void
    {
        $this->applicator = new StatsApplicator();
    }

    #[Test]
    public function shouldApplyStatsOnQuery(): void
    {
        $entity = new StatsDummyEntity();
        $this->assertTrue($this->applicator->supportEntity($entity));
        $this->applicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(EntityApplicator::class, $entity),
            $this->applicator,
        ]);

        $this->assertStringContainsString('rows=' . $entity->getNumberOfRows(), $queryUri);
        $this->assertStringContainsString('stats=true', $queryUri);

        $statsFields = $entity->getStatsFields();
        foreach ($statsFields as $field) {
            $this->assertStringContainsString('stats.field=' . $field, $queryUri);
        }
    }
}
