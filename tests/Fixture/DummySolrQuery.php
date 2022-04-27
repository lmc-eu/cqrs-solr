<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\Fixture;

use Lmc\Cqrs\Solr\Query\AbstractSolrSelectQuery;
use Solarium\QueryType\Select\Query\Query;

class DummySolrQuery extends AbstractSolrSelectQuery
{
    public function __construct(private array $fields)
    {
    }

    public function prepareSelect(Query $select): Query
    {
        return $select->addFields($this->fields);
    }
}
