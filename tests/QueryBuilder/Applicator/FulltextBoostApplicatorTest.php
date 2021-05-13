<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\FulltextInterface;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FulltextBigramBoostDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FulltextBoostDummyEntity;

class FulltextBoostApplicatorTest extends ApplicatorTestCase
{
    private FulltextBoostApplicator $fulltextBoostApplicator;

    protected function setUp(): void
    {
        $this->fulltextBoostApplicator = new FulltextBoostApplicator();
    }

    /**
     * @test
     */
    public function shouldApplyBoostOnQuery(): void
    {
        $entity = new FulltextBoostDummyEntity();
        $this->assertTrue($this->fulltextBoostApplicator->supportEntity($entity));
        $this->fulltextBoostApplicator->setEntity($entity);

        $this->assertApplyFulltextOnQuery($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->fulltextBoostApplicator,
        ]);

        $this->assertStringContainsString('bq=' . $entity->getBoostQuery(), $queryUri);
        $this->assertStringContainsString('ps=' . $entity->getPhraseSlop(), $queryUri);
    }

    private function assertApplyFulltextOnQuery(FulltextInterface $entity): void
    {
        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(FulltextApplicator::class, $entity),
            $this->getApplicator(EntityApplicator::class, $entity),
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
    public function shouldApplyBoostAndBigramOptionsOnQuery(): void
    {
        $entity = new FulltextBigramBoostDummyEntity();
        $this->assertTrue($this->fulltextBoostApplicator->supportEntity($entity));
        $this->fulltextBoostApplicator->setEntity($entity);

        $this->assertApplyFulltextOnQuery($entity);

        $fulltextBigramApplicator = $this->getApplicator(FulltextBigramApplicator::class, $entity);

        $queryUri = $this->getCustomQueryUri([
            $fulltextBigramApplicator,
            $this->fulltextBoostApplicator,
        ]);

        $this->assertStringContainsString('bq=' . $entity->getBoostQuery(), $queryUri);
        $this->assertStringContainsString('ps=' . $entity->getPhraseSlop(), $queryUri);

        $this->assertStringContainsString('pf2=' . implode('&pf2=', $entity->getPhraseBigramFields()), $queryUri);
    }
}
