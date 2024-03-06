<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\Fixture\BaseDummyEntity;
use PHPUnit\Framework\Attributes\Test;

class EntityApplicatorTest extends AbstractApplicatorTestCase
{
    private EntityApplicator $entityApplicator;

    protected function setUp(): void
    {
        $this->entityApplicator = new EntityApplicator();
    }

    #[Test]
    public function shouldApplyEntityInterface(): void
    {
        $baseEntity = new BaseDummyEntity();
        $this->assertTrue($this->entityApplicator->supportEntity($baseEntity));
        $this->entityApplicator->setEntity($baseEntity);

        $queryUri = $this->getCustomQueryUri([$this->entityApplicator]);

        $this->assertStringContainsString('rows=' . $baseEntity->getNumberOfRows(), $queryUri);
        $this->assertStringContainsString('fl=' . implode(',', $baseEntity->getFields()), $queryUri);
    }

    #[Test]
    public function shouldUsePlaceholderForAllFields(): void
    {
        $baseEntity = new BaseDummyEntity('', []);
        $this->assertTrue($this->entityApplicator->supportEntity($baseEntity));
        $this->entityApplicator->setEntity($baseEntity);

        $queryUri = $this->getCustomQueryUri([$this->entityApplicator]);

        $this->assertStringContainsString('rows=' . $baseEntity->getNumberOfRows(), $queryUri);
        $this->assertStringContainsString('fl=*', $queryUri);
    }
}
