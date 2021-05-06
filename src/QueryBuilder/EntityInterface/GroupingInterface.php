<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\EntityInterface;

interface GroupingInterface extends EntityInterface
{
    public function getGroupingField(): string;

    public function getGroupingLimit(): int;

    public function getNumberOfGroups(): bool;

    public function getMainResult(): bool;
}
