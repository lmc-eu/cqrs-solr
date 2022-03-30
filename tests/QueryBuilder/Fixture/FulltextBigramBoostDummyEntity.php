<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Fixture;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\FulltextBigramInterface;
use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\FulltextBoostInterface;

class FulltextBigramBoostDummyEntity implements FulltextBigramInterface, FulltextBoostInterface
{
    private bool $isEDisMaxEnabled;
    private bool $useGlobalEdismax;

    public function __construct(bool $isEdismaxEnabled = true, bool $useGlobalEdismax = true)
    {
        $this->isEDisMaxEnabled = $isEdismaxEnabled;
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

    public function getPhraseBigramFields(): array
    {
        return ['bigram-field~5'];
    }

    public function getBoostQuery(): string
    {
        return 'boosted-field:value^5.0';
    }

    public function getPhraseSlop(): int
    {
        return 2;
    }

    public function isEDisMaxEnabled(): bool
    {
        return $this->isEDisMaxEnabled;
    }

    public function useEDisMaxGlobally(): bool
    {
        return $this->useGlobalEdismax;
    }
}
