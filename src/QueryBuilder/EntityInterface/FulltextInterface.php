<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\EntityInterface;

interface FulltextInterface extends EntityInterface
{
    public function getKeywords(): array;

    public function getDefaultQueryOperator(): string;

    public function getQueryFields(): array;

    public function getPhraseFields(): array;

    public function getQueryAlternative(): string;

    public function getMinimumMatch(): ?string;

    public function getTie(): float;

    public function isEDisMaxEnabled(): bool;
}
