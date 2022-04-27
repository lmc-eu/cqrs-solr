<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\QueryBuilder\Query;

use Lmc\Cqrs\Solr\Query\AbstractSolrSelectQuery;
use Lmc\Cqrs\Solr\QueryBuilder\Applicator\ApplicatorInterface;
use Solarium\QueryType\Select\Query\Query;

final class BuilderPrototypeQuery extends AbstractSolrSelectQuery
{
    /** @param ApplicatorInterface[] $applicators */
    public function __construct(private array $applicators)
    {
    }

    public function prepareSelect(Query $select): Query
    {
        foreach ($this->applicators as $applicator) {
            $applicator->applyOnQuery($select);
        }

        return $select;
    }
}
