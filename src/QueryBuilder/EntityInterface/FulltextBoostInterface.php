<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\EntityInterface;

interface FulltextBoostInterface extends FulltextInterface
{
    public function getBoostQuery(): ?string;

    public function getPhraseSlop(): ?int;
}
