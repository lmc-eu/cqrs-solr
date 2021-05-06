<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Fixture;

use Lmc\Cqrs\Solr\QueryBuilder\EntityInterface\SortInterface;
use Solarium\QueryType\Select\Query\Query;

class SortDummyEntity implements SortInterface
{
    public function getNumberOfRows(): int
    {
        return 20;
    }

    public function getFields(): array
    {
        return [];
    }

    public function getSorts(): array
    {
        return [
            'score' => Query::SORT_DESC,
            'field' => Query::SORT_ASC,
        ];
    }
}
