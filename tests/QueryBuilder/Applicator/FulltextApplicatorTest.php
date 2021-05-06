<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\Fixture\DisabledEDisMaxDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FulltextDummyEntity;

class FulltextApplicatorTest extends ApplicatorTestCase
{
    private FulltextApplicator $fulltextApplicator;

    protected function setUp(): void
    {
        $this->fulltextApplicator = new FulltextApplicator();
    }

    /**
     * @test
     */
    public function shouldApplyFulltextOnQuery(): void
    {
        $entity = new FulltextDummyEntity();
        $this->assertTrue($this->fulltextApplicator->supportEntity($entity));
        $this->fulltextApplicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(EntityApplicator::class, $entity),
            $this->fulltextApplicator,
        ]);

        $this->assertStringContainsString('rows=' . $entity->getNumberOfRows(), $queryUri);

        $this->assertStringContainsString('q=' . implode(' ', $entity->getKeywords()), $queryUri);
        $this->assertStringContainsString('q.op=' . $entity->getDefaultQueryOperator(), $queryUri);
        $this->assertStringContainsString('defType=edismax', $queryUri);
        $this->assertStringContainsString('qf=' . implode(' ', $entity->getQueryFields()), $queryUri);
        $this->assertStringContainsString('pf=' . implode(' ', $entity->getPhraseFields()), $queryUri);
        $this->assertStringContainsString('q.alt=' . $entity->getQueryAlternative(), $queryUri);
        $this->assertStringContainsString('tie=' . $entity->getTie(), $queryUri);
        $this->assertStringContainsString('mm=' . $entity->getMinimumMatch(), $queryUri);
    }

    /**
     * @test
     */
    public function shouldApplyFulltextOnQueryWithDisabledEDisMax(): void
    {
        $entity = new DisabledEDisMaxDummyEntity();
        $this->assertTrue($this->fulltextApplicator->supportEntity($entity));
        $this->fulltextApplicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(EntityApplicator::class, $entity),
            $this->fulltextApplicator,
        ]);

        $this->assertStringContainsString('rows=' . $entity->getNumberOfRows(), $queryUri);
        $this->assertStringContainsString('q=' . implode(' ', $entity->getKeywords()), $queryUri);
        $this->assertStringContainsString('q.op=' . $entity->getDefaultQueryOperator(), $queryUri);

        $this->assertStringNotContainsString('defType=edismax', $queryUri);
        $this->assertStringNotContainsString('qf=' . implode(' ', $entity->getQueryFields()), $queryUri);
        $this->assertStringNotContainsString('pf=' . implode(' ', $entity->getPhraseFields()), $queryUri);
        $this->assertStringNotContainsString('q.alt=' . $entity->getQueryAlternative(), $queryUri);
        $this->assertStringNotContainsString('tie=' . $entity->getTie(), $queryUri);
        $this->assertStringNotContainsString('mm=' . $entity->getMinimumMatch(), $queryUri);
    }
}
