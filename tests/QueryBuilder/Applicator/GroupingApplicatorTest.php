<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\Fixture\GroupingDummyEntity;

class GroupingApplicatorTest extends ApplicatorTestCase
{
    private GroupingApplicator $groupingApplicator;

    protected function setUp(): void
    {
        $this->groupingApplicator = new GroupingApplicator();
    }

    /**
     * @test
     */
    public function shouldApplyGroupingOnQuery(): void
    {
        $entity = new GroupingDummyEntity();
        $this->assertTrue($this->groupingApplicator->supportEntity($entity));
        $this->groupingApplicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(EntityApplicator::class, $entity),
            $this->groupingApplicator,
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
    }
}
