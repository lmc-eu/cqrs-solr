<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\Fixture\BaseDummyEntity;

class EntityApplicatorTest extends ApplicatorTestCase
{
    private EntityApplicator $entityApplicator;

    protected function setUp(): void
    {
        $this->entityApplicator = new EntityApplicator();
    }

    /**
     * @test
     */
    public function shouldApplyEntityInterface(): void
    {
        $baseEntity = new BaseDummyEntity();
        $this->assertTrue($this->entityApplicator->supportEntity($baseEntity));
        $this->entityApplicator->setEntity($baseEntity);

        $queryUri = $this->getCustomQueryUri([$this->entityApplicator]);

        $this->assertStringContainsString('rows=' . $baseEntity->getNumberOfRows(), $queryUri);
        $this->assertStringContainsString('fl=' . implode(',', $baseEntity->getFields()), $queryUri);
    }
}
