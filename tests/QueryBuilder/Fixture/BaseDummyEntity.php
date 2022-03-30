<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Fixture;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\EntityInterface;
use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\FulltextInterface;
use Solarium\QueryType\Select\Query\Query;

class BaseDummyEntity implements EntityInterface, FulltextInterface
{
    private string $query;
    private array $fields;
    private bool $useGlobalEdismax;

    public function __construct(string $query = '', array $fields = ['jds', '*', 'score'], bool $useGlobalEdismax = true)
    {
        $this->query = $query;
        $this->fields = $fields;
        $this->useGlobalEdismax = $useGlobalEdismax;
    }

    public function getNumberOfRows(): int
    {
        return 10;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getKeywords(): array
    {
        return array_filter(explode(' ', $this->query));
    }

    public function getDefaultQueryOperator(): string
    {
        return Query::QUERY_OPERATOR_AND;
    }

    public function getQueryFields(): array
    {
        return $this->getFields();
    }

    public function getPhraseFields(): array
    {
        return [];
    }

    public function getQueryAlternative(): string
    {
        return '*:*';
    }

    public function getMinimumMatch(): ?string
    {
        return null;
    }

    public function getTie(): float
    {
        return 0.2;
    }

    public function isEDisMaxEnabled(): bool
    {
        return false;
    }

    public function useEDisMaxGlobally(): bool
    {
        return $this->useGlobalEdismax;
    }
}
