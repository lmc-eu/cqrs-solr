<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\Fixture\FulltextApplicatorTrait;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FulltextBigramBoostDummyEntity;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FulltextBoostDummyEntity;

class FulltextBoostApplicatorTest extends ApplicatorTestCase
{
    use FulltextApplicatorTrait;

    private FulltextBoostApplicator $fulltextBoostApplicator;

    protected function setUp(): void
    {
        $this->fulltextBoostApplicator = new FulltextBoostApplicator();
    }

    /**
     * @test
     * @dataProvider provideGlobalEdismax
     */
    public function shouldApplyBoostOnQuery(bool $isGlobalEdismax): void
    {
        $entity = new FulltextBoostDummyEntity(true, $isGlobalEdismax);
        $this->assertTrue($this->fulltextBoostApplicator->supportEntity($entity));
        $this->fulltextBoostApplicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(FulltextApplicator::class, $entity),
            $this->getApplicator(EntityApplicator::class, $entity),
        ]);

        $this->assertApplyFulltextOnQuery($entity, $queryUri);

        $queryUri = $this->getCustomQueryUri([
            $this->fulltextBoostApplicator,
        ]);

        $parameters = $this->parseQueryUriParameters($queryUri);

        if ($isGlobalEdismax) {
            $boostQuery = $this->assertParameterExists('bq', $parameters);
            $this->assertSame($entity->getBoostQuery(), $boostQuery);

            $phraseSlop = $this->assertParameterExists('ps', $parameters);
            $this->assertSame($entity->getPhraseSlop(), (int) $phraseSlop);
        } else {
            $query = $this->assertParameterExists('q', $parameters);

            $this->assertStringContainsString('bq=$boostQuery', $query);
            $boostQuery = $this->assertParameterExists('boostQuery', $parameters);
            $this->assertSame($entity->getBoostQuery(), $boostQuery);

            $this->assertStringContainsString('ps=$phraseSlop', $query);
            $phraseSlop = $this->assertParameterExists('phraseSlop', $parameters);
            $this->assertSame($entity->getPhraseSlop(), (int) $phraseSlop);
        }
    }

    /**
     * @test
     * @dataProvider provideGlobalEdismax
     */
    public function shouldApplyBoostAndBigramOptionsOnQuery(bool $isGlobalEdismax): void
    {
        $entity = new FulltextBigramBoostDummyEntity(true, $isGlobalEdismax);
        $this->assertTrue($this->fulltextBoostApplicator->supportEntity($entity));
        $this->fulltextBoostApplicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(FulltextApplicator::class, $entity),
            $this->getApplicator(EntityApplicator::class, $entity),
        ]);

        $this->assertApplyFulltextOnQuery($entity, $queryUri);

        $fulltextBigramApplicator = $this->getApplicator(FulltextBigramApplicator::class, $entity);

        $queryUri = $this->getCustomQueryUri([
            $fulltextBigramApplicator,
            $this->fulltextBoostApplicator,
        ]);

        $parameters = $this->parseQueryUriParameters($queryUri);

        if ($isGlobalEdismax) {
            $phraseBigramFields = $this->assertParameterExists('pf2', $parameters);
            $this->assertStringContainsString(implode(' ', $entity->getPhraseBigramFields()), $phraseBigramFields);

            $boostQuery = $this->assertParameterExists('bq', $parameters);
            $this->assertSame($entity->getBoostQuery(), $boostQuery);

            $phraseSlop = $this->assertParameterExists('ps', $parameters);
            $this->assertSame($entity->getPhraseSlop(), (int) $phraseSlop);
        } else {
            $query = $this->assertParameterExists('q', $parameters);

            $this->assertStringContainsString('pf2=$phraseBigramFields', $query);
            $phraseBigramFields = $this->assertParameterExists('phraseBigramFields', $parameters);
            $this->assertSame(implode(' ', $entity->getPhraseBigramFields()), $phraseBigramFields);

            $this->assertStringContainsString('bq=$boostQuery', $query);
            $boostQuery = $this->assertParameterExists('boostQuery', $parameters);
            $this->assertSame($entity->getBoostQuery(), $boostQuery);

            $this->assertStringContainsString('ps=$phraseSlop', $query);
            $phraseSlop = $this->assertParameterExists('phraseSlop', $parameters);
            $this->assertSame($entity->getPhraseSlop(), (int) $phraseSlop);
        }
    }

    /**
     * @test
     * @dataProvider provideGlobalEdismax
     */
    public function shouldApplyFulltextOnQueryWithDisabledEDisMax(bool $isGlobalEdismax): void
    {
        $entity = new FulltextBigramBoostDummyEntity(false, $isGlobalEdismax);
        $this->assertTrue($this->fulltextBoostApplicator->supportEntity($entity));
        $this->fulltextBoostApplicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(EntityApplicator::class, $entity),
            $this->getApplicator(FulltextApplicator::class, $entity),
            $this->fulltextBoostApplicator,
        ]);

        $this->assertApplyFulltextOnQueryWithoutEdisMax($entity, $queryUri);

        $parameters = $this->parseQueryUriParameters($queryUri);

        $this->assertArrayNotHasKey('bq', $parameters);
        $this->assertArrayNotHasKey('boostQuery', $parameters);

        $this->assertArrayNotHasKey('ps', $parameters);
        $this->assertArrayNotHasKey('phraseSlop', $parameters);

        $this->assertArrayNotHasKey('pf2', $parameters);
        $this->assertArrayNotHasKey('phraseBigramFields', $parameters);
    }
}
