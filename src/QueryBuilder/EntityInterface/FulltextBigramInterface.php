<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\EntityInterface;

interface FulltextBigramInterface extends FulltextInterface
{
    public function getPhraseBigramFields(): array;
}
