<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Fixture;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\GroupingInterface;

class GroupingDummyEntity implements GroupingInterface
{
    public function getNumberOfRows(): int
    {
        return 20;
    }

    public function getFields(): array
    {
        return [];
    }

    public function getGroupingField(): string
    {
        return 'grouping-field';
    }

    public function getGroupingLimit(): int
    {
        return 50;
    }

    public function getNumberOfGroups(): bool
    {
        return true;
    }

    public function getMainResult(): bool
    {
        return true;
    }
}
