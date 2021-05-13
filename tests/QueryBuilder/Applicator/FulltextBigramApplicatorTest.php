<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FulltextBigramDummyEntity;

class FulltextBigramApplicatorTest extends ApplicatorTestCase
{
    private FulltextBigramApplicator $fulltextBigramApplicator;

    protected function setUp(): void
    {
        $this->fulltextBigramApplicator = new FulltextBigramApplicator();
    }

    /**
     * @test
     */
    public function shouldApplyFulltextOnQuery(): void
    {
        $entity = new FulltextBigramDummyEntity();
        $this->assertTrue($this->fulltextBigramApplicator->supportEntity($entity));
        $this->fulltextBigramApplicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(EntityApplicator::class, $entity),
            $this->getApplicator(FulltextApplicator::class, $entity),
            $this->fulltextBigramApplicator,
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

        $this->assertStringContainsString('pf2=' . implode('&pf2=', $entity->getPhraseBigramFields()), $queryUri);
    }
}
