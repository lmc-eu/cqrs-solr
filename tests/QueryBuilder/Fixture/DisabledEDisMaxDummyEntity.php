<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Fixture;

class DisabledEDisMaxDummyEntity extends FulltextDummyEntity
{
    public function isEDisMaxEnabled(): bool
    {
        return false;
    }
}
