<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\EntityInterface;

interface FulltextInterface extends EntityInterface
{
    public function getKeywords(): array;

    /**
     * TIP: If you are using EDisMax, use getMinimumMatch instead
     * @see isEDisMaxEnabled()
     * @see getMinimumMatch()
     */
    public function getDefaultQueryOperator(): string;

    public function getQueryFields(): array;

    public function getPhraseFields(): array;

    public function getQueryAlternative(): string;

    /**
     * TIP: You can use minimum match as a default Query Operator alternative:
     * - OR  -> mm=1
     * - AND -> mm=100%
     */
    public function getMinimumMatch(): ?string;

    public function getTie(): float;

    public function isEDisMaxEnabled(): bool;

    public function useEDisMaxGlobally(): bool;
}
