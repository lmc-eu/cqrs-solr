<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\EntityInterface;

interface SortInterface extends EntityInterface
{
    public function getSorts(): array;
}
