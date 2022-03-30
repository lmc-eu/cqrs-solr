<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Fixture;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\FulltextInterface;

class FulltextDummyEntity implements FulltextInterface
{
    private bool $useGlobalEdismax;

    public function __construct(bool $useGlobalEdismax = true)
    {
        $this->useGlobalEdismax = $useGlobalEdismax;
    }

    public function getNumberOfRows(): int
    {
        return 20;
    }

    public function getFields(): array
    {
        return [];
    }

    public function getKeywords(): array
    {
        return ['key', 'words'];
    }

    public function getDefaultQueryOperator(): string
    {
        return 'AND';
    }

    public function getQueryFields(): array
    {
        return ['field'];
    }

    public function getPhraseFields(): array
    {
        return ['field'];
    }

    public function getQueryAlternative(): string
    {
        return '*:*';
    }

    public function getMinimumMatch(): string
    {
        return '2';
    }

    public function getTie(): float
    {
        return 0.2;
    }

    public function isEDisMaxEnabled(): bool
    {
        return true;
    }

    public function useEDisMaxGlobally(): bool
    {
        return $this->useGlobalEdismax;
    }
}
