<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\Fixture\FulltextApplicatorTrait;
use Lmc\Cqrs\Solr\QueryBuilder\Fixture\FulltextBigramDummyEntity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class FulltextBigramApplicatorTest extends AbstractApplicatorTestCase
{
    use FulltextApplicatorTrait;

    private FulltextBigramApplicator $fulltextBigramApplicator;

    protected function setUp(): void
    {
        $this->fulltextBigramApplicator = new FulltextBigramApplicator();
    }

    #[Test]
    #[DataProvider('provideGlobalEdismax')]
    public function shouldApplyFulltextOnQuery(bool $isGlobalEdismax): void
    {
        $entity = new FulltextBigramDummyEntity(true, $isGlobalEdismax);
        $this->assertTrue($this->fulltextBigramApplicator->supportEntity($entity));
        $this->fulltextBigramApplicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(EntityApplicator::class, $entity),
            $this->getApplicator(FulltextApplicator::class, $entity),
            $this->fulltextBigramApplicator,
        ]);

        $this->assertApplyFulltextOnQuery($entity, $queryUri);

        $parameters = $this->parseQueryUriParameters($queryUri);

        if ($isGlobalEdismax) {
            $phraseBigramFields = $this->assertParameterExists('pf2', $parameters);
            $this->assertStringContainsString(implode(' ', $entity->getPhraseBigramFields()), $phraseBigramFields);
        } else {
            $query = $this->assertParameterExists('q', $parameters);

            $this->assertStringContainsString('pf2=$phraseBigramFields', $query);
            $phraseBigramFields = $this->assertParameterExists('phraseBigramFields', $parameters);
            $this->assertSame(implode(' ', $entity->getPhraseBigramFields()), $phraseBigramFields);
        }
    }

    #[Test]
    #[DataProvider('provideGlobalEdismax')]
    public function shouldApplyFulltextOnQueryWithDisabledEDisMax(bool $isGlobalEdismax): void
    {
        $entity = new FulltextBigramDummyEntity(false, $isGlobalEdismax);
        $this->assertTrue($this->fulltextBigramApplicator->supportEntity($entity));
        $this->fulltextBigramApplicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(EntityApplicator::class, $entity),
            $this->getApplicator(FulltextApplicator::class, $entity),
            $this->fulltextBigramApplicator,
        ]);

        $this->assertApplyFulltextOnQueryWithoutEdisMax($entity, $queryUri);

        $parameters = $this->parseQueryUriParameters($queryUri);

        $this->assertArrayNotHasKey('pf2', $parameters);
        $this->assertArrayNotHasKey('phraseBigramFields', $parameters);
    }
}
