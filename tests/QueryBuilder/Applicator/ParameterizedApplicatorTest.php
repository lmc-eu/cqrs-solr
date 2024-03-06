<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Applicator;

use Lmc\Cqrs\Solr\QueryBuilder\Fixture\ParameterizedDummyEntity;
use PHPUnit\Framework\Attributes\Test;

class ParameterizedApplicatorTest extends AbstractApplicatorTestCase
{
    private ParameterizedApplicator $parameterizedApplicator;

    protected function setUp(): void
    {
        $this->parameterizedApplicator = new ParameterizedApplicator();
    }

    #[Test]
    public function shouldApplySortOnQuery(): void
    {
        $entity = new ParameterizedDummyEntity();
        $this->assertTrue($this->parameterizedApplicator->supportEntity($entity));
        $this->parameterizedApplicator->setEntity($entity);

        $queryUri = $this->getCustomQueryUri([
            $this->getApplicator(EntityApplicator::class, $entity),
            $this->parameterizedApplicator,
        ]);

        $this->assertStringContainsString('rows=' . $entity->getNumberOfRows(), $queryUri);

        $paramsExpects = [];
        foreach ($entity->getParams() as $paramField => $paramValue) {
            $paramsExpects[] = sprintf('%s=%s', $paramField, $paramValue);
        }
        $this->assertStringContainsString(implode(',', $paramsExpects), $queryUri);
    }
}
