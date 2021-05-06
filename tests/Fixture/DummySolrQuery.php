<?php declare(strict_types=1);

namespace Lmc\Cqrs\Solr\Fixture;

use Lmc\Cqrs\Solr\Query\AbstractSolrSelectQuery;
use Solarium\QueryType\Select\Query\Query;

class DummySolrQuery extends AbstractSolrSelectQuery
{
    private array $fields;

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function prepareSelect(Query $select): Query
    {
        return $select->addFields($this->fields);
    }
}
