<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\Fixture;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\FulltextInterface;

trait FulltextApplicatorTrait
{
    public function provideGlobalEdismax(): array
    {
        return [
            // isGlobal
            'global' => [true],
            'local' => [false],
        ];
    }

    private function assertApplyFulltextOnQuery(FulltextInterface $entity, string $queryUri): void
    {
        $parameters = $this->parseQueryUriParameters($queryUri);

        $rows = $this->assertParameterExists('rows', $parameters);
        $this->assertSame((string) $entity->getNumberOfRows(), $rows);

        $queryOperator = $this->assertParameterExists('q.op', $parameters);
        $this->assertSame($queryOperator, $entity->getDefaultQueryOperator());

        $queryAlternative = $this->assertParameterExists('q.alt', $parameters);
        $this->assertSame($entity->getQueryAlternative(), $queryAlternative);

        if ($entity->useEDisMaxGlobally()) {
            $query = $this->assertParameterExists('q', $parameters);
            $this->assertSame(implode(' ', $entity->getKeywords()), $query);

            $defType = $this->assertParameterExists('defType', $parameters);
            $this->assertSame('edismax', $defType);

            $queryFields = $this->assertParameterExists('qf', $parameters);
            $this->assertSame(implode(' ', $entity->getQueryFields()), $queryFields);

            $phraseFields = $this->assertParameterExists('pf', $parameters);
            $this->assertStringContainsString(implode(' ', $entity->getPhraseFields()), $phraseFields);
        } else {
            $query = $this->assertParameterExists('q', $parameters);
            $this->assertStringStartsWith('{!type=edismax', $query);
            $this->assertStringEndsWith('}', $query);
            $this->assertStringContainsString('v=$userQuery', $query);
            $this->assertStringContainsString('mm=' . $entity->getMinimumMatch(), $query);
            $this->assertStringContainsString('tie=' . $entity->getTie(), $query);
            $this->assertStringContainsString('qf=$queryFields', $query);
            $this->assertStringContainsString('pf=$phraseFields', $query);

            $this->assertArrayNotHasKey('defType', $parameters);

            $queryFields = $this->assertParameterExists('queryFields', $parameters);
            $this->assertSame(implode(' ', $entity->getQueryFields()), $queryFields);

            $phraseFields = $this->assertParameterExists('phraseFields', $parameters);
            $this->assertSame(implode(' ', $entity->getPhraseFields()), $phraseFields);
        }
    }

    private function assertApplyFulltextOnQueryWithoutEdisMax(FulltextInterface $entity, string $queryUri): void
    {
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
