<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\Fixture\FulltextApplicatorTrait;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\DisabledEDisMaxDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FulltextDummyEntity;

class FulltextApplicatorTest extends ApplicatorTestCase
{
    use FulltextApplicatorTrait;

    private FulltextApplicator $fulltextApplicator;

    protected function setUp(): void
    {
        $this->fulltextApplicator = new FulltextApplicator();
    }

    /**
     * @test
     * @dataProvider provideGlobalEdismax
     */
    public function shouldApplyFulltextOnQuery(bool $isGlobalEdismax): void
    {
        $entity = new FulltextDummyEntity($isGlobalEdismax);
        $this->assertTrue($this->fulltextApplicator->supportEntity($entity));
        $this->fulltextApplicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(EntityApplicator::class, $entity),
            $this->fulltextApplicator,
        ]);

        $this->assertApplyFulltextOnQuery($entity, $queryUri);
    }

    /**
     * @test
     * @dataProvider provideGlobalEdismax
     */
    public function shouldApplyFulltextOnQueryWithDisabledEDisMax(bool $isGlobalEdismax): void
    {
        $entity = new DisabledEDisMaxDummyEntity($isGlobalEdismax);
        $this->assertTrue($this->fulltextApplicator->supportEntity($entity));
        $this->fulltextApplicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(EntityApplicator::class, $entity),
            $this->fulltextApplicator,
        ]);

        $this->assertApplyFulltextOnQueryWithoutEdisMax($entity, $queryUri);
    }
}
